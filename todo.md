# Todo 👷

## Relelase 1 ✨

* Add the ability to run 50/50 draws where members can purchase tickets (externally).The admin will input these tickets that users purchased and the user will be notified of their purchase of the tickets has been registered. Winners are notified by email. Define new draw (e.g. draw name, draw start datetime and draw end datetime). A draw will have a link to a tickets table (each ticket will be linked to the owning userid). On the date and time that the draw ends, no more tickets can be purchased. A "Select Random Winner" button will be displayed when the draw is over and a random ticket will be selected as a winner for the draw. That winning ticket will be have a flag that will be updated as winner. The draw will also have a flag that will set the draw in read only mode once the winner is defined. Add a new flag for admin to recieve or not recieve 50/50 draw result emails. Send an email to the winning ticket owner. Send an email to all the admins that have the 50/50 email notification set on and summary of the draw and statistics around the draw (e.g. duration, winner details, tickets sold, total amount collected, prize amount). Add the new user email preference on settings/notifications page.This is a new app so just change the existing migrations and seeds. I will do an artisan migrate:fresh -seed.

* Add a timezone to the settings table to set what timezone the application is in. Default to Toronto. Add this to the settings in the filament setting page. This is a new app so just change the existing migrations and seeds. I will do an artisan migrate:fresh -seed.

* test the refund button

* Make the adoption form questions dynamic. 

## Release 2 ⚡


Add a timezone to the settings table to set what timezone the application is in. Default to Toronto. Add this to the settings in the filament setting page. This is a new app so just change the existing migrations and seeds. I will do an artisan migrate:fresh -seed.

Here is the method and approach that I like. For new users default them to toronto. Allow the users the abilty to change their timezone in the front end profile page. Modify the filament user edit to allow editing this new timezone field. Here is at cut and paste of the method:

Method 3: User-Specific Timezone Conversion Using Carbon
The most user-friendly approach is to store all datetime values in UTC in your database but display them in each user’s local timezone. This is considered a best practice.

Step 1: Store Dates in UTC: Ensure your application and database timezone are set to UTC in config/app.php and your database settings.

All Eloquent model timestamps (created_at, updated_at) and any dates you save will be in UTC.

Step 2: Add a timezone Column to Your Users Table: Here’s what you need to do.

1
php artisan make:migration add_timezone_to_users_table
1
2
3
4
5
6
7
// In the generated migration file
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('timezone')->default('UTC');
    });
}
Run the migration:

1
php artisan migrate
Step 3: Convert Times for Display: When retrieving a date from the database, convert it to the user’s timezone using Carbon. You can do this in your controllers, views, or via a model accessor.

Example in a Blade View:

1
2
// $post->created_at is stored as UTC in the database
{{ $post->created_at->timezone(auth()->user()->timezone)->format('Y-m-d g:i A') }}
Example using a Model Accessor:

1
2
3
4
5
6
7
// In your Post model
public function getCreatedAtLocalAttribute()
{
    return $this->created_at->timezone(auth()->user()->timezone);
}
// Now you can use it in your view as:
// {{ $post->created_at_local }}