<?php

namespace Tests\Unit;

use App\Models\ExamResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExamResultGradeTest extends TestCase
{
    #[Test]
    public function marks_below_passing_return_f(): void
    {
        $this->assertSame('F', ExamResult::calculateGrade(32, 100, 33));
        $this->assertSame('F', ExamResult::calculateGrade(33, 100, 34));
        $this->assertSame('F', ExamResult::calculateGrade(0, 100, 33));
    }

    #[Test]
    public function passing_marks_returns_d(): void
    {
        $this->assertSame('D', ExamResult::calculateGrade(33, 100, 33));
        $this->assertSame('D', ExamResult::calculateGrade(39, 100, 33));
    }

    #[Test]
    public function percentage_bands_match_bangladesh_standard(): void
    {
        $this->assertSame('C', ExamResult::calculateGrade(40, 100, 33));
        $this->assertSame('B', ExamResult::calculateGrade(50, 100, 33));
        $this->assertSame('A-', ExamResult::calculateGrade(60, 100, 33));
        $this->assertSame('A', ExamResult::calculateGrade(70, 100, 33));
        $this->assertSame('A+', ExamResult::calculateGrade(80, 100, 33));
    }

    #[Test]
    public function handles_full_and_partial_marks(): void
    {
        $this->assertSame('A+', ExamResult::calculateGrade(100, 100, 33));
        $this->assertSame('A', ExamResult::calculateGrade(35, 50, 16));
        $this->assertSame('F', ExamResult::calculateGrade(15, 50, 16));
    }
}
