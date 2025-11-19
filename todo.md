# Todo 👷

## Relelase 1 ✨

* Add the ability to run 50/50 draws where members can purchase tickets (externally).The admin will input these tickets that users purchased and the user will be notified of their purchase of the tickets has been registered. Winners are notified by email. Define new draw (e.g. draw name, draw start datetime and draw end datetime). A draw will have a link to a tickets table (each ticket will be linked to the owning userid). On the date and time that the draw ends, no more tickets can be purchased. A "Select Random Winner" button will be displayed when the draw is over and a random ticket will be selected as a winner for the draw. That winning ticket will be have a flag that will be updated as winner. The draw will also have a flag that will set the draw in read only mode once the winner is defined. Add a new flag for admin to recieve or not recieve 50/50 draw result emails. Send an email to the winning ticket owner. Send an email to all the admins that have the 50/50 email notification set on and summary of the draw and statistics around the draw (e.g. duration, winner details, tickets sold, total amount collected, prize amount). Add the new user email preference on settings/notifications page.This is a new app so just change the existing migrations and seeds. I will do an artisan migrate:fresh -seed.

* Make the adoption form questions dynamic.

## Release 2 ⚡
