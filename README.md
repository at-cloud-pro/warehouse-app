# Warehouse
Portal providing documents preview and files sharing in AT Cloud Platform.

## Before you start

### Host OS

This project is well tested on **macOS Sonoma** with **Docker Desktop**. Other platforms should work fine, but we do not
provide support for them.

### Git

To enhance security, we require you to configure your local development environment in the way we can recognise your
identity. Please set up local `git` to add metadata on each push. You can do that by setting:

```shell
# replace <your name and surname> with your real name
git config user.name "<your name and surname>"

# replace <your email> with your real email that ends on @gi.org.pl
# if you don't have that email, contact administrator
git config user.email "<your email>"

# we usually recommend to sign off commits with SSH key you already have in your system
git config gpg.format ssh

# replace <path to your ssh key> with your real SSH public key
# example: ~/.ssh/id_ed25519.pub
git config user.signingkey <path to your ssh key>
```

You need to repeat these steps on each new machine you'll be using for development. **Pull requests containing unsigned
commits will not be possible to merge.**

## Development

This project use:

- PHP 8.3
- Symfony framework
- Doctrine ORM

as a leading software and **DigitalOcean** as a cloud provider. Locally, the environment is set up using Docker Compose.

### Setting project up

To set up project easily, copy the local environment configuration:
```shell
ln -s ./docker/dev/compose.override.yaml .
```
> __Note for macOS (Apple Silicon CPUs) users__
>
> By default `linux/amd64` image is built. Use `.compose.override.yaml` file and add there information below to build more
> optimized image version:
> ```yaml
> services:
>  app:
>    platform: linux/arm64
>  #rest of the file
> ```

Then, run the docker compose to start the project:

```shell
docker compose up -d --build
```

It will build the project and run it in the background. To check if it's running correctly, visit your browser under
[http://localhost](http://localhost). To see the logs stream, run:

```shell
docker compose logs -f app
```

### Running the project tips

> **Do not execute `bin/console` or any other commands regarding project from your host machine, use container shell
> instead.**
> For example, to clear app cache, run:
>
> ```shell
> docker compose exec app bin/console cache:clear
> ```
>
> It will execute the `bin/console cache:clear` command in the container shell.

To shut down the containers run:

```shell
docker compose down
```

## Testing

To run tests, execute the following commands:

```bash
# unit tests
docker compose exec app vendor/bin/phpunit

# e2e tests
docker compose exec app vendor/bin/phpunit --format progress
```

We use **PHPUnit** for unit tests and **Behat** for end-to-ends. Unit tests are stored in the `test` directory with the
structure the same as the original code, while end-to-end tests are stored in the `features` directory.

## Release & deploy

> Before merging Pull Request to the main branch, make sure to bump the project version in the:
>
> - `composer.json` file, line `2`
> - `compose.yaml` file, line `7`
> - `Dockerfile` file, line `3`
>
> Not bumping the version will result in the release failure. There is a step in CI that will protect the main branch
> from being merged without bumping the version.

**Release** is the step where we:

- a git tag to last commit in given PR is pushed
- final, production Docker image is built
- image is tagged with correct semver version
- tagged image is pushed **GitHub Image Repository**

**Deploy** is a step, where the code is deployed to **DigitalOcean**.

**Release and deploy is automatic in this project.**

## Changelog

This project is using [semantic versioning](https://semver.org/) and
[conventional commits](https://conventionalcommits.org). See
[dedicated GitHub releases list](https://github.com/at-cloud-pro/warehouse-app/releases)) for more information.

## License

This project is owned by Oskar Barcz operating under brand **AT Cloud**, and it's not open source. Every usage outside
dedicated URL ([workspace.atcloud.pro](https://workspace.atcloud.pro)) requires written consent from Oskar Barcz.
