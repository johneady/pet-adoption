<x-mail::message>
# Congratulations! Your Application Has Been Approved! 🎉

Dear {{ $user->name }},

We are thrilled to inform you that your adoption application for **{{ $pet->name }}** has been **approved**! After careful review, we believe you will provide a wonderful, loving home for {{ $pet->name }}.

**Application Details:**
- **Pet:** {{ $pet->name }} ({{ $pet->species->name ?? 'Pet' }})
- **Approved:** {{ now()->timezone($user->timezone)->format('F j, Y') }}
- **Status:** {{ ucfirst($application->status) }}

**Next Steps:**

1. **Review and Sign Adoption Agreement** - We'll send you the official adoption agreement to review and sign
2. **Schedule Pickup** - Contact us to arrange a convenient time to bring {{ $pet->name }} home
3. **Prepare Your Home** - Make sure you have all necessary supplies (food, bed, toys, etc.)
4. **Meet Your New Family Member** - Get ready for lots of love and companionship!

**Important Information:**
- Please bring a valid ID when picking up {{ $pet->name }}
- We'll provide you with {{ $pet->name }}'s medical records and care instructions
- Our team is here to support you during the transition period

<x-mail::button :url="route('dashboard')">
View Your Dashboard
</x-mail::button>

Thank you for choosing to adopt and giving {{ $pet->name }} a forever home. We can't wait to see the wonderful life you'll build together!

If you have any questions or need assistance, please don't hesitate to reach out to our team.

Warmest congratulations,<br>
{{ App\Models\Setting::get('site_name') }}
</x-mail::message>
