#NATIS Bot

This is a simple bot I made for fun that will check the natis online system to track the status of a drivers license application.

The bot will check the status of the application and then post that status to telegram

##Requirements

- Natis User Account (Can be setup at https://online.natis.gov.za/#/)
- Create a telegram bot and channel to post to

### Setup the telegram bot

1) In telegram search in messages and message `@botfather` and type `/new bot`
2) Follow the on screen prompts to create your bot and copy the API key given at the end.
3) Create a channel in telegram
4) Add your bot to the channel
5) Get the channel ID by sending a message to the channel and then forwarding that message to `@jsondumpbot`
6) Copy the channel ID

## Installation

1) Run `composer install` and fill in the required parameters
2) There is no next step

Once you have installed the needed dependencies you can run `bin/console poll:natis:license_status` to poll natis for you license progress

You can them cron it on whatever server is running for you to run as often as like. It's a goverment department so daily should be enough ;-)