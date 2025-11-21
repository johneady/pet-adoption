# Todo 👷

## Relelase 1 ✨

* look at all the database cascading delete and make sure they make sense. But don't allow a species, breed, to be deleted if they have associated pets

* fix the n+1 error on the /admin/draws page

## Release 2 ⚡

I will perform a artisan migrate:fresh --seed once you are completed.

Add soft deletes to User, Pet, BlogPost models to preserve historical integrity

Restrict Species/Breed deletions if pets exist (change CASCADE to RESTRICT)
