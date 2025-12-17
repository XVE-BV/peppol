<?php

declare(strict_types=1);

use Xve\LaravelPeppol\Actions\LookupParticipantAction;
use Xve\LaravelPeppol\Support\Participant;

// Known Peppol-capable Belgian participant
it('looks up Belgian participant with BE prefix', function () {
    $action = app(LookupParticipantAction::class);
    $result = $action->execute('BE0805374964');

    expect($result)->toBeInstanceOf(Participant::class)
        ->and($result->participantId)->not->toBeEmpty()
        ->and($result->capable)->toBeTrue();
});

it('looks up Belgian participant with be prefix (lowercase)', function () {
    $action = app(LookupParticipantAction::class);
    $result = $action->execute('be0805374964');

    expect($result)->toBeInstanceOf(Participant::class)
        ->and($result->participantId)->not->toBeEmpty()
        ->and($result->capable)->toBeTrue();
});
