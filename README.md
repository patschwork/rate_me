# rate_me
Rate_me is a tool to give things a personal rating (mostly for drinks and food). 

To remember good 🙂 👍<br>
...and bad 😕 👎 things

# Installation

1. Create a Postgres database
2. Clone this repo
3. Install PHP, LiquiBase, Python and a webserver of your choice (e.g. Apache or Nginx) 
4. Adjust the database deployment config in `database_model/liquibase/deploy_config_DEVLINUX_SRV.ini`, `database_model/liquibase/DEVLINUX_SRV.properties`
5. Run the database deployment script `database_model/liquibase/deploy_01_DEVLINUX_SRV.sh` 
6. Create a virtual Host in Apache or Nginx
7. Copy the template `frontend/common/config/main-local-DEFAULT.php` to `frontend/common/config/main-local.php` and adjust the database settings
8. Copy the template `frontend/frontend/config/main-local-DEFAULT.php` to `frontend/frontend/config/main-local.php` and set a random value for the cookie validation key.

Done.
Open the application in your browser of choice.

# Configuration

## Type

Types are the kind of items you want give a rating. 

Some examples
- Coffee
- Wine
- Restaurants
- Beer
- Whiskey
- ...

## Special characteristics for type (optional)

rate_me is able to track more than standard rating with a comment. 
 
### Example for coffee

#### type characteristics
- Origin of the coffee bean (textfield)
- Ration of Arabica and Robusta coffee beans (drowdown)
- Which coffee machines are suited for this coffee (multiple dropdown)

#### rating characteristics
- What was the weight of the milled coffee (textfield)
- Which coffee machine/model was used (dependant dropdown)
- Which coffee drink was produced? Coffee, Espresso, Coffie verkeert, Latte Macciato (dropdown)

### Example for wine
#### type characteristics
- Red, white wine (dropdown)
- Year, when the wine was produced (textfield)

This makes it more complex to track your experience.

## How to configure
This is currently only possible via database editor. 

If you are interessted how to do this, please ticket a issue in GitHub