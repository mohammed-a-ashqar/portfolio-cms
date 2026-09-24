<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\QuoteStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuoteStatusTest extends TestCase
{
    /** @return array<string, array{QuoteStatus, QuoteStatus, bool}> */
    public static function transitions(): array
    {
        return [
            'new -> in review' => [QuoteStatus::New, QuoteStatus::InReview, true],
            'in review -> quoted' => [QuoteStatus::InReview, QuoteStatus::Quoted, true],
            'quoted -> accepted' => [QuoteStatus::Quoted, QuoteStatus::Accepted, true],
            'accepted -> closed' => [QuoteStatus::Accepted, QuoteStatus::Closed, true],
            'new -> accepted skips review' => [QuoteStatus::New, QuoteStatus::Accepted, false],
            'new -> quoted skips review' => [QuoteStatus::New, QuoteStatus::Quoted, false],
            'closed is terminal' => [QuoteStatus::Closed, QuoteStatus::New, false],
            'accepted cannot be rejected' => [QuoteStatus::Accepted, QuoteStatus::Rejected, false],
        ];
    }

    #[Test]
    #[DataProvider('transitions')]
    public function it_enforces_the_workflow(QuoteStatus $from, QuoteStatus $to, bool $allowed): void
    {
        $this->assertSame($allowed, $from->canTransitionTo($to));
    }

    #[Test]
    public function every_non_terminal_status_can_be_closed(): void
    {
        foreach (QuoteStatus::cases() as $status) {
            if ($status === QuoteStatus::Closed) {
                continue;
            }

            $this->assertTrue($status->canTransitionTo(QuoteStatus::Closed), "{$status->value} should be closable");
        }
    }

    #[Test]
    public function open_statuses_are_the_ones_awaiting_action(): void
    {
        $this->assertTrue(QuoteStatus::New->isOpen());
        $this->assertTrue(QuoteStatus::Quoted->isOpen());
        $this->assertFalse(QuoteStatus::Accepted->isOpen());
        $this->assertFalse(QuoteStatus::Closed->isOpen());
    }
}
