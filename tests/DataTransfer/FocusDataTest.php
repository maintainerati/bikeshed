<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Tests\DataTransfer;

use Maintainerati\Bikeshed\DataTransfer\FocusData;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maintainerati\Bikeshed\DataTransfer\FocusData
 */
class FocusDataTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $this->markTestIncomplete();
    }

    public function testCreateFromIterable(): void
    {
        $this->markTestIncomplete();
    }

    public function testIsFocused(): void
    {
        $this->markTestIncomplete();
    }

    public function testIsCreate(): void
    {
        $this->markTestIncomplete();
    }

    public function testGetFormName(): void
    {
        $this->markTestIncomplete();
    }

    public function providerType(): iterable
    {
        yield 'All empty' => [FocusData::TYPE_EVENT, null, null, null, null, false];
        yield 'All empty create' => [FocusData::TYPE_EVENT, null, null, null, null, true];
        yield 'Event' => [FocusData::TYPE_EVENT, '6686a1b7-4fb2-4e4f-b691-ccebf3cbdcee', null, null, null, false];
        yield 'Event create session' => [FocusData::TYPE_SESSION, '6686a1b7-4fb2-4e4f-b691-ccebf3cbdcee', null, null, null, true];
        yield 'Session' => [FocusData::TYPE_SESSION, '6686a1b7-4fb2-4e4f-b691-ccebf3cbdcee', '5d706a4c-6ee4-4f12-8ae3-dae9c6718f63', null, null, false];
        yield 'Session create space' => [FocusData::TYPE_SPACE, '6686a1b7-4fb2-4e4f-b691-ccebf3cbdcee', '5d706a4c-6ee4-4f12-8ae3-dae9c6718f63', null, null, true];
        yield 'Space' => [
            FocusData::TYPE_SPACE,
            '6686a1b7-4fb2-4e4f-b691-ccebf3cbdcee',
            '5d706a4c-6ee4-4f12-8ae3-dae9c6718f63',
            'b4a50218-53d5-43fd-ad49-ddb75ef62d8c',
            null,
            false,
        ];
        yield 'Space create note' => [
            FocusData::TYPE_NOTE,
            '6686a1b7-4fb2-4e4f-b691-ccebf3cbdcee',
            '5d706a4c-6ee4-4f12-8ae3-dae9c6718f63',
            'b4a50218-53d5-43fd-ad49-ddb75ef62d8c',
            null,
            true,
        ];
        yield 'Note' => [
            FocusData::TYPE_NOTE,
            '6686a1b7-4fb2-4e4f-b691-ccebf3cbdcee',
            '5d706a4c-6ee4-4f12-8ae3-dae9c6718f63',
            'b4a50218-53d5-43fd-ad49-ddb75ef62d8c',
            'b29c3870-c686-4635-9283-116d35fabdd1',
            false,
        ];
    }

    /**
     * @dataProvider providerType
     */
    public function testGetType(string $expected, ?string $eventId, ?string $sessionId, ?string $spaceId, ?string $noteId, bool $create): void
    {
        $focusData = FocusData::create($create);
        $focusData
            ->setEventId($eventId)
            ->setSessionId($sessionId)
            ->setSpaceId($spaceId)
            ->setNoteId($noteId)
        ;

        self::assertSame($expected, $focusData->getType());
    }

    public function testGettersSetters(): void
    {
        $this->markTestIncomplete();
    }
}
