# Survey Feedback

## Overview
### Description
This project is a survey feedback application designed to collect and manage responses from participants. It follows Domain-Driven Design (DDD) principles, organizing business logic within the `app/Domains` directory for clarity and maintainability. The application provides endpoints for survey creation, participation, and feedback collection, serving as a foundation for more advanced survey management features.

### Product Decisions
1. A survey can have multiple fields
2. Kept the field types very simple - `text`, `textarea` and `radio`
3. For ease, just used a JSON structure for the `radio` options, Frontend is just a `copy-separated` list.
4. Users can belong to many companies, but for ease of use, just didn't go into the full team/multitenant approach.
5. On user creation, a new company is created for said user.
6. Participants can respond to a survey only once (using the email as unique check)

### Future Product Decisions
1. If we are to consider closed surveys, we could have added a `survey_participants` table where we will be inviting people into the survey.
2. All listing endpoints could use request filters and also pagination in real world scenarios.
3. We could have dependent fields, etc.., kept field simplicity for test.

### Code Architecture Decisions
1. For the DDD architecture, we could have created a `domains` folder on the root directory and added a new composer `autoload.psr-4` entry such as `"Domain\\": "domain/"` but I chose to go with `app/Domains` for this quick project.
2. Used `laravel/breeze` for authentication and boilerplate.
3. Extracted vue components from the boilerplate to be used for the main dashboard.
4. No state manager in vue, just kept it very simple.

#### Where are my Form Requests
1. I'm glad you asked, over the years, I have come to enjoy using `spatie/laravel-data` package as form requests and also as DTOs.
2. I can write a lot of why, but it makes it nice to use the Data objects in `Actions` or `Service` classes without really tying it to a form request or the most notorious, just an array.
3. The same data can also be used to transform the contents or even act as a JSON resource.

### Database Decisions
1. Some indexes are missing on purpose. Didn't really go into full optimization yet.
2. Some entities could have more elaborate fields.

### Infrastructure Decisions
1. Could install `laravel/horizon` to monitor queue workers, however for now, we are just using supervisor to manage queues. I personally also use a lot of aws/sqs in cloud environment.
2. Scheduler is running as a php process inside `supervisord` instead of using system cron.

## Project Setup
### Prerequisites
0. Of course, clone the project to your local. :P
1. You need to install `Docker Desktop` or `OrbStack` either one works fine.
2. You will need to have `make` installed on your CMD for easy commands.
3. Why `make`? Relax, we are not doing any hardcore C or C++ :D, it's just a simpler means to group together docker commands to make it easier. Just checkout the `Makefile`.

### Initial Setup
1. Copy `.env.example` to `.env`
2. First make sure there are no services using the ports needed for this service, especially port 80 for (app), 3306 (for db), etc...
3. In case you have conflicts on ports, in your `.env` you can update `APP_PORT`, `DB_FORWARD_PORT`, `REDIS_FORWARD_PORT`, `MAILPIT_WEB_PORT`, `MAILPIT_SMTP_PORT` and `VITE_PORT` to your liking.
4. Run `make setup` from the project root. It will do some bootstrapping and setup the docker containers.

### Running the FE
1. We are using vite to compile the Vue components,
2. In another terminal, simply run `make frontend`.
3. It should start the vite dev process and FE should work as expected.

### Restarting Background Workers
1. The survey summary process depends on background workers. A `scheduler` and two (2) `queue-workers`.
2. After making any backend code changes and env changes, that affect the summary changes, you need to restart the processes.
3. Simply run `make restart-bg-workers`.
4. Have fun.

### Accessing UI
1. Visit the app using `http://localhost` if you changed port from 80 then add then port `http://locahost:${APP_PORT}`
2. We seed a test user, Email: `test@survey.com` and Password: `testing1234`. **[ONLY FOR TESTING]**
3. Viola, click on surveys, you should see one survey in there with a couple of responses
