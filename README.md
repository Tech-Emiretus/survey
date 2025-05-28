# Notes
1. For the DDD architecture, we could have created a `domains` folder on the root directory and added a new composer `autoload.psr-4` entry such as `"Domain\\": "domain/"` but I chose to go with `app/Domains` for this quick project.
2. If we are to consider closed surveys, we could have added a `survey_participants` table where we will be inviting peopel into the survey.
