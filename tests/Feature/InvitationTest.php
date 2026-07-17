<?php

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationCreated;
use Illuminate\Support\Facades\Notification;

test('a SuperAdmin can invite an Admin into a new company', function () {
    Notification::fake();
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->from(route('dashboard'))
        ->post(route('invitations.store'), [
            'email' => 'new-admin@example.com',
            'role' => 'admin',
            'company_name' => 'New Company',
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('invitation_url');

    $company = Company::where('name', 'New Company')->firstOrFail();

    $this->assertDatabaseHas('invitations', [
        'company_id' => $company->id,
        'email' => 'new-admin@example.com',
        'role' => 'admin',
    ]);

    Notification::assertSentOnDemand(InvitationCreated::class);
});

test('a SuperAdmin cannot invite a Member', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->post(route('invitations.store'), [
            'email' => 'member@example.com',
            'role' => 'member',
            'company_name' => 'New Company',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('companies', 0);
    $this->assertDatabaseCount('invitations', 0);
});

test('an Admin can invite an Admin or Member only into their company', function (string $role) {
    Notification::fake();
    $company = Company::factory()->create();
    $admin = User::factory()->admin($company)->create();

    $this->actingAs($admin)
        ->post(route('invitations.store'), [
            'email' => "{$role}@example.com",
            'role' => $role,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('invitations', [
        'company_id' => $company->id,
        'email' => "{$role}@example.com",
        'role' => $role,
    ]);
})->with(['admin', 'member']);

test('a Member cannot invite users', function () {
    $member = User::factory()->member()->create();

    $this->actingAs($member)
        ->post(route('invitations.store'), [
            'email' => 'other@example.com',
            'role' => 'member',
        ])
        ->assertForbidden();
});

test('an invited user can accept a valid invitation', function () {
    $company = Company::factory()->create();
    $invitation = Invitation::create([
        'company_id' => $company->id,
        'email' => 'invited@example.com',
        'role' => 'member',
        'token' => 'valid-invitation-token',
        'expires_at' => now()->addDay(),
    ]);

    $this->post(route('invitations.register', $invitation->token), [
        'name' => 'Invited User',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'email' => 'invited@example.com',
        'company_id' => $company->id,
        'role' => 'member',
    ]);
    $this->assertDatabaseMissing('invitations', ['id' => $invitation->id]);
});

test('the invitation acceptance page renders aligned account fields', function () {
    $company = Company::factory()->create(['name' => 'Aligned Company']);
    $invitation = Invitation::create([
        'company_id' => $company->id,
        'email' => 'aligned@example.com',
        'role' => 'admin',
        'token' => 'aligned-invitation-token',
        'expires_at' => now()->addDay(),
    ]);

    $this->get(route('invitations.accept', $invitation->token))
        ->assertOk()
        ->assertSeeText('Create account')
        ->assertSeeText('You were invited to join Aligned Company')
        ->assertSeeText('as Admin.')
        ->assertSeeText('aligned@example.com')
        ->assertSee('name="name"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="password_confirmation"', false)
        ->assertSee('autocomplete="name"', false)
        ->assertSee('autocomplete="new-password"', false)
        ->assertSee('w-full', false);
});

test('an expired invitation cannot be accepted', function () {
    $company = Company::factory()->create();
    $invitation = Invitation::create([
        'company_id' => $company->id,
        'email' => 'expired@example.com',
        'role' => 'member',
        'token' => 'expired-invitation-token',
        'expires_at' => now()->subMinute(),
    ]);

    $this->get(route('invitations.accept', $invitation->token))->assertNotFound();
});
