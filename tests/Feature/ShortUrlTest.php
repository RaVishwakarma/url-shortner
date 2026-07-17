<?php

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;

test('an admin can create a short URL', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('short-urls.store'), ['original_url' => 'https://example.com/admin'])
        ->assertRedirect(route('short-urls.index'));

    $this->assertDatabaseHas('short_urls', [
        'company_id' => $admin->company_id,
        'user_id' => $admin->id,
        'original_url' => 'https://example.com/admin',
    ]);
});

test('a member can create a short URL', function () {
    $member = User::factory()->member()->create();

    $this->actingAs($member)
        ->post(route('short-urls.store'), ['original_url' => 'https://example.com/member'])
        ->assertRedirect(route('short-urls.index'));

    $this->assertDatabaseHas('short_urls', [
        'company_id' => $member->company_id,
        'user_id' => $member->id,
    ]);
});

test('a SuperAdmin cannot create a short URL', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->post(route('short-urls.store'), ['original_url' => 'https://example.com'])
        ->assertForbidden();

    $this->assertDatabaseCount('short_urls', 0);
});

test('a SuperAdmin sees short URLs from every company', function () {
    $firstUser = User::factory()->member()->create();
    $secondUser = User::factory()->member()->create();
    $firstUrl = createShortUrl($firstUser, 'firstcode');
    $secondUrl = createShortUrl($secondUser, 'secondcode');

    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('short-urls.index'))
        ->assertOk()
        ->assertViewHas('urls', fn ($urls) => $urls->pluck('id')->sort()->values()->all()
            === collect([$firstUrl->id, $secondUrl->id])->sort()->values()->all());
});

test('an admin sees all URLs in their company but none from another company', function () {
    $company = Company::factory()->create();
    $admin = User::factory()->admin($company)->create();
    $colleague = User::factory()->member($company)->create();
    $otherUser = User::factory()->member()->create();
    $ownCompanyUrl = createShortUrl($colleague, 'company1');
    $otherCompanyUrl = createShortUrl($otherUser, 'company2');

    $this->actingAs($admin)
        ->get(route('short-urls.index'))
        ->assertOk()
        ->assertViewHas('urls', fn ($urls) => $urls->contains($ownCompanyUrl)
            && ! $urls->contains($otherCompanyUrl));
});

test('a member sees only URLs they created', function () {
    $company = Company::factory()->create();
    $member = User::factory()->member($company)->create();
    $colleague = User::factory()->member($company)->create();
    $memberUrl = createShortUrl($member, 'member01');
    $colleagueUrl = createShortUrl($colleague, 'member02');

    $this->actingAs($member)
        ->get(route('short-urls.index'))
        ->assertOk()
        ->assertViewHas('urls', fn ($urls) => $urls->contains($memberUrl)
            && ! $urls->contains($colleagueUrl));
});

test('a short URL is publicly resolvable and redirects to its original URL', function () {
    $shortUrl = createShortUrl(
        User::factory()->member()->create(),
        'public01',
        'https://example.com/original',
    );

    $this->get(route('short-urls.redirect', $shortUrl->short_code))
        ->assertRedirect('https://example.com/original');
});

test('an unknown short code returns 404', function () {
    $this->get(route('short-urls.redirect', 'unknown1'))->assertNotFound();
});

function createShortUrl(
    User $user,
    string $code,
    string $originalUrl = 'https://example.com',
): ShortUrl {
    return ShortUrl::create([
        'company_id' => $user->company_id,
        'user_id' => $user->id,
        'original_url' => $originalUrl,
        'short_code' => $code,
    ]);
}
