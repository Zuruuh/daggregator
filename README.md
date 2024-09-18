# Requirements

If you use NixOS, you can simply use the provided flake with the `nix develop`
command to get started. Otherwise you will need to install the following
software:

- Docker
- Python 3.12
- Bun
- Biome
- PHP 8.3
- The Symfony CLI
- Composer
- Pulumi
- GnuPG and Sops
- Just

# Architecture

This project is composed of multiple services:

## [`The twitter scrapper`](twitter-scrapper)

This service uses the `twscrape` python library to access the GraphQL twitter
api, and can be accessed through an http api using `fastapi`. The tweets are
then streamed back on the `/tweets` endpoint so they can be used while still
fetching the next ones.

## [`The worker and API`](api)

Both are merged into a single symfony project for simplicity, this service can
load and save medias from reddit and twitter on a daily basis with the Symfony
Scheduler component, and acts as a bridge between the user interface and the
meilisearch instance.

## [`The infrastructure`](infra)

This represents the AWS infra used for the project using Pulumi, an IaC tool
similar to terraform. For now there's only an S3 bucket for storing images, but
in the future i'd like to add the helm chart used in prod on my VPS.
