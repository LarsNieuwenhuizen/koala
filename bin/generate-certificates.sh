#!/usr/bin/env bash
source ~/.koala/.koala
if [[ -f ~/.koala/.koala.local ]]; then
    source ~/.koala/.koala.local
fi

hostnames=($@)

set -eu

generate_root_certicate() {

    openssl genrsa \
        -des3 \
        -out "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.key" \
        -passout pass:password \
        4096

    openssl rsa \
        -in "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.key" \
        -out "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.key" \
        -passin pass:password

    openssl req \
        -x509 \
        -new \
        -nodes \
        -key "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.key" \
        -sha256 \
        -days 1024 \
        -out "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.crt" \
        -subj "/C=${CSR_C}/ST=${CSR_ST}/L=${CSR_L}/O=${CSR_O}/OU=${CSR_OU}"

    if [ "$(uname -s)" == "Linux" ]; then
        sudo cp "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.crt" /usr/local/share/ca-certificates/
        sudo update-ca-certificates
    fi

    if [ "$(uname -s)" == "Darwin" ]; then
        sudo security add-trusted-cert \
            -d \
            -r trustRoot \
            -k /Library/Keychains/System.keychain "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.crt"
    fi
}

generate_certificate() {
    certificateHostname=$1

    cat >"${CERTIFICATE_PATH}/v3.ext" <<EOL
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names

[alt_names]
DNS.1 = ${certificateHostname}
EOL

    openssl req \
        -new \
        -sha256 \
        -nodes \
        -out "${CERTIFICATE_PATH}/${certificateHostname}.csr" \
        -newkey rsa:2048 \
        -keyout "${CERTIFICATE_PATH}/${certificateHostname}.key" \
        -subj "/C=${CSR_C}/ST=${CSR_ST}/L=${CSR_L}/O=${CSR_O}/OU=${CSR_OU}/CN=${certificateHostname}"

    openssl x509 \
        -req -in "${CERTIFICATE_PATH}/${certificateHostname}.csr" \
        -CA "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.crt" \
        -CAkey "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.key" \
        -CAcreateserial \
        -out "${CERTIFICATE_PATH}/${certificateHostname}.crt" \
        -days 500 \
        -sha256 \
        -extfile "${CERTIFICATE_PATH}/v3.ext"

    cat "${CERTIFICATE_PATH}/${certificateHostname}.key" >"${CERTIFICATE_PATH}/${certificateHostname}.pem"
    cat "${CERTIFICATE_PATH}/${certificateHostname}.crt" >>"${CERTIFICATE_PATH}/${certificateHostname}.pem"
}

generate_traefik_conf() {
    certificateHostname=$1

    cat >"${CERTIFICATE_CONFD_PATH}/${certificateHostname}.yaml" <<EOL
---

tls:
  certificates:
    - certFile: /certs/${certificateHostname}.crt
      keyFile: /certs/${certificateHostname}.key
EOL

}

mkdir -p ${KOALA_CERTIFICATES_PATH}/{certs,conf.d}

CERTIFICATE_PATH=$(realpath ${KOALA_CERTIFICATES_PATH}/certs)
CERTIFICATE_CONFD_PATH=$(realpath ${KOALA_CERTIFICATES_PATH}/conf.d)
CA_CERTIFICATE_NAME="_koala-ca-root-certificate"

CSR_C="${CSR_C:-NL}"
CSR_ST="${CSR_ST:-Web}"
CSR_L="${CSR_L:-Nederland}"
CSR_O="${CSR_O:-Local}"
CSR_OU="${CSR_OU:-Development}"

if [ ! -f "${CERTIFICATE_PATH}/${CA_CERTIFICATE_NAME}.crt" ]; then
    generate_root_certicate
fi

for certificateHostname in "${hostnames[@]}"; do
    if [ ! -f "${CERTIFICATE_PATH}/$certificateHostname.crt" ]; then
        generate_certificate $certificateHostname
        generate_traefik_conf $certificateHostname
    fi
done
