# Laradock CLI helper

This project aims to set a couple of conventions to ease developing using the already easy [Laradock](https://github.com/laradock/laradock) project.

## Dependencies

* You need Docker and docker-compose installed (latest versions)
* You need PHP 7.1 locally
* You also need git configured locally

## Conventions

There's nothing wrong with a bit of conventions over a nice tool to ease development. This CLI tool will help you setup and use Laradock in a shared project environment. The idea is that you are going to have a shared laradock setup for all your projects.

Before you start using it, you need to arrange your projects in the same folder, so something like this:

```bash
/Projects
├── project-a
└── project-b

2 directories, 0 file
```

Laradock will be configured in the same level of your projects, and you are to configure a vhosts for each of your projects that you want to use with the laradock setup.

## Getting Started

[TODO]

You can install the `ldk` CLI tool via composer:

```bash
composer global require tonysm/ldk-cli
```

After doing this, you should have the `ldk` CLI helper available.

### Setting up

The first thing you should do is cd'ing into the Projects folder and run:

```bash
cd Projects
ldk init
> Cloning the laradock folder, this might take some time..
> Configuring the your global setup...
> Done!
```

After this command, you should have an `.ldk/` folder at your Project's root folder, like this:

```bash
/Projects
├── .ldk
├── project-a
└── project-b

3 directories, 0 files
```

### Booting our base containers

This step is needed, because any configuration is going to be added to the Docker containers as well. Run:

```bash
cd Projects/project-a
ldk up
> Detected no container running...
> Starting containers: nginx, workspace
> Done!
```

Your workspace containers are now running, but you haven't configured your sites yet.

### Adding your project vhost

[TODO]

To add your site vhost, you need to run:

```bash
cd Projects/project-a
ldk sites:add project-a public/
> Adding new site http://project-a.ldk/ with document root: public/
> Site added!
> Restarting the nginx container...
> Done!
> Do you want to edit your hosts file now (don't worry, we are opening up your editor of choice so you can edit it yourself) [Y/n]? yes
...
> Done!
```

### Adding a database

You also need to add a database for your project, you can do it by:

```bash
ldk db:add projecta
> Which database do you want to use?
> [1] MySQL
> [2] Postgres
> Option: 1
> MySQL container is not running. Booting it...
> Done!
> Creating `projecta` database in your MySQL container
> Done!
```

Done! Your database is now created. You can use these credentials (depending on the database provider you chose):

MySQL:

[MISSING]

PostgreSQL:

[MISSING]

MariaDB:

[MISSING]

Remember to change your `.env` with these configs.

### Running migrations

Now we need to run the migrations for our projecta. Run:

```bash
cd Projects/project-a
ldk artisan migrate
> Goinging to inside the workspace container at `~/project-a` folder...
> php artisan migrate
> Migration table created.
> Running migration 00000_create_users_table...
> ...
> Done!
```

You can also use the `ldk artisan` command from your project, it will forward any calls to the artisan inside the Laradock container for you.

### Adding Redis

Laradock has lots of services you can use on your apps, to demonstrate that we are going to add Redis to our application. First, you need to boot the `redis` service, like so:

```bash
cd Projects/project-a
ldk up redis
> Booting up redis container...
> Done!
```

Great, now you can change your redis configuration to point to:

```bash
REDIS_HOST=redis
```

and you are good to go.

