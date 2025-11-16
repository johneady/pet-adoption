<x-mail::message>
# Update on Your Adoption Application

Dear {{ $user->name }},

Thank you so much for your interest in adopting **{{ $pet->name }}** and for taking the time to complete an adoption application with us. We truly appreciate your desire to provide a loving home to a pet in need.

After careful consideration of all applications received, we have decided to move forward with another applicant for {{ $pet->name }}. This was not an easy decision, as we received many wonderful applications from caring individuals like yourself.

**Why This Happens:**

Pet adoption matching is a thoughtful process where we consider many factors including lifestyle compatibility, home environment, experience level, and the specific needs of each pet. Sometimes we find another applicant whose circumstances align slightly better with a particular pet's unique requirements.

**We Encourage You to Keep Looking:**

- We have many other wonderful pets looking for loving homes
- New pets become available for adoption regularly
- Your perfect match may be waiting for you!

<x-mail::button :url="route('pets.index')">
Browse Available Pets
</x-mail::button>

**Resources for Your Pet Adoption Journey:**
- Visit our website to see all available pets
- Sign up for email alerts about new arrivals
- Reach out to our team if you'd like help finding the right match

Please don't be discouraged. The right pet for you is out there, and we would be honored to help you find your new best friend. We welcome you to submit another application for any pet that captures your heart.

Thank you for your understanding and for your commitment to pet adoption. We hope to help you find your perfect companion soon!

With warm regards,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
