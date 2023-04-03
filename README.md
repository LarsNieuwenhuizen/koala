# Koala

A Docker network with containers as a base layer for development.

This setup uses Traefik as a reverse proxy to serve as an entrypoint for your applications during development

- [Traefik](https://traefik.io/traefik/)

## Requirements
- Docker
- Docker-compose
- PHP7.4+
- Composer

## How to install
Run
```bash
curl https://raw.githubusercontent.com/LarsNieuwenhuizen/koala/1.8.0/bin/installer.sh | bash
```

Koala is now installed and placed the koala console script in your local bin $PATH.

Restart your terminal

You can now run:
```bash
koala
```

You'll see this:

```bash
========================================================

     ▄▄▄   ▄ ▄▄▄▄▄▄▄ ▄▄▄▄▄▄▄ ▄▄▄     ▄▄▄▄▄▄▄
    █   █ █ █       █       █   █   █       █
    █   █▄█ █   ▄   █   ▄   █   █   █   ▄   █
    █      ▄█  █ █  █  █▄█  █   █   █  █▄█  █
    █     █▄█  █▄█  █       █   █▄▄▄█       █
    █    ▄  █       █   ▄   █       █   ▄   █
    █▄▄▄█ █▄█▄▄▄▄▄▄▄█▄▄█ █▄▄█▄▄▄▄▄▄▄█▄▄█ █▄▄█

========================================================
        
Console Tool

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help             Display help for a command
  list             List commands
  restart          Re-start the Koala network
  self-update      Update to the latest version
  start            Start the Koala network
  stop             Start the Koala network
 compose
  compose:service  Start other services
 create
  create:project   Create an empty php/nginx project running on the Koala network
```

## Starting Koala
If you start, it will by default start:
- Traefik
- Mariadb
- Postgres
- Mailcatcher

These are the base services created in the docker network "koala".

**note**:
You might be asked for your system user password the first time.
This is the case if you need administrator privileges to create the root certificate authority for koala.

## SSL
Certificates are generated for the services where you apply the configuration for it.

The TLD (top level domain) used is docker.
A root certificate is generated to sign all other application certificates.
So you can trust the root certificate and all other signed certificates will be valid by parent certificate.

You can find the root certificate here:
- ~/.koala-persistent/ssl/certs/_koala-ca-root-certificate.crt

Simply trust it as CA in the browser or your machine globally.

### Traefik
What traefik allows you to do is make services/containers accessible via http(s), tcp or udp routes.
So say for example you have a nginx webservice container in your project.
Simply add it to the koala network in docker and add Traefik labels to add the correct routning.

You can access the Traefik dashboard on [localhost:8000](http://localhost:8000)

More documentation and automation on how to apply and use this in projects will follow...

### Maria db
Mariadb is started on port 3308 and accessible on host on 
- localhost:3308

Or in de Koala network on 

- mariadb103.services.docker:3308

Root username & password are:
- root / password

### PostgreSQL
Postgres is started on port 5434 and accessible on host on
- locahost:5434

Or in the Koala network on

- postgres11.services.docker:5434

Root username & password are:
- root / password

### Mailcatcher
For testing mails there is a container running.
This is accessible in the browser on [mail.docker](https://mail.docker)

So be sure to point mail.docker to 127.0.0.1 and/or ::1 in your hosts file.

`127.0.01  mail.docker`

`::1  mail.docker`

A certificate is generated on creation of the container.
See [The SSL section](#SSL) for more on that

## Compose services

Aside from the basic containers that are created you can choose for the option "Compose service".

This shows the extra services you can start.
For now this is just Elasticsearch 7.17 & Kibana.

When you start this you'll have an elasticsearch node available on:
- localhost:9202

Or in the koala network as:
- elasticsearch7.services.docker:9202

Kibana will be started (this can take a few seconds), you can access it on:
- [kibana.elasticsearch7.docker](https://kibana.elasticsearch7.docker)

Also be sure to point "kibana.elasticsearch7.docker" to 127.0.0.1 and/or ::1.

`/etc/hosts`
```text
127.0.07    kibana.elasticsearch7.docker
::1         kibana.elasticsearch7.docker
```

## Create a new php/nginx project

If you want to create a project with a php & nginx container run:

```bash
koala create:project
```

- Enter a domain name
- Follow steps given in the out
