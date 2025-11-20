# Todo 👷

## Relelase 1 ✨

* Add a purchase tickets button on the front end /draws page for the current draw. This will take them to a new purchase tickets page (that requries authentication), and its a purchase tickets page. It shows the details of the current draw, the prices for tickets and a form to select how many tickets they want (dropdown). When they submit the form, an email will be sent to the admins with this ticket purchase request. There will be a new ticket_purchase indicator on the admins user profile that controls if they receive the ticket purchase email request. Modify the filament user edit, and the front end profile for the admin notifications. I will perform a artisan migrate:fresh --seed once you are completed. Also, when tickets are registed, send an email to the customer with the details around the ticket purchase as well as key details around the draw (e.g. name, date, ticket numbers purchased, current prize, tickets sold, etc...)

* make the ticket numbers that are generated match this format. First digit is the id of the draw, followed by 5 digits starting at 00001 and incrementing.

## Release 2 ⚡

I will perform a artisan migrate:fresh --seed once you are completed.
