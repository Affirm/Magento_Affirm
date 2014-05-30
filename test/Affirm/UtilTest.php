<?php

class Affirm_UtilTest extends UnitTestCase {
    public function testFormatMoney() {
        $this->assertSame("10.45", Affirm_Util::formatMoney(10.45));
    }

    /* If implementation uses money_format to convert a float value to a string,
     * the current locale will affect the result. This is a sanity check to keep
     * the implementer honest.
     */
    public function testFormatMoneyNotAffectedByLocale() {
        setlocale(LC_MONETARY, 'en_US');
        $this->assertSame("10010.45", Affirm_Util::formatMoney(10010.45));

        setlocale(LC_MONETARY, 'nl_NL');
        $this->assertSame("10.45", Affirm_Util::formatMoney(10.45));

    }

    public function testFormatMoneyZero() {
        $this->assertSame("0.00", Affirm_Util::formatMoney(0));
    }

    /*
     * test formatCents
     *
     * Testing Methodology: Hit (1) each order of magnitude up to 10^5 cents making
     * sure to hit (2) negative quantities and (3) quantities with trailing 0's.
     *
     * 10^5 is sufficient. An incorrect implementation could introduce ','
     * comma's separating the hundreds and thousands places.
     */

    public function testFormatCentsEmpty() {
        $this->assertSame(0, Affirm_Util::formatCents(null));
    }

    public function testFormatCentsBugsCaught() {
        $floatAmount = 2299.99;
        $naive = 229998;
        $expected = 229999;
        // NB: weird things happen to floats
        $this->assertSame($naive, (int) (2299.99 * 100));
        $this->assertSame($expected, Affirm_Util::formatCents(2299.99));
    }

    // 10^0
    public function testFormatCentsOnes() {
        $this->assertSame(0, Affirm_Util::formatCents(0));
        $this->assertSame(7, Affirm_Util::formatCents(0.07));
        $this->assertSame(-7, Affirm_Util::formatCents(-0.07));
    }

    // 10^1
    public function testFormatCentsTens() {
        $this->assertSame(10, Affirm_Util::formatCents(0.10));
        $this->assertSame(12, Affirm_Util::formatCents(0.12));
        $this->assertSame(-12, Affirm_Util::formatCents(-0.12));
    }

    // 10^2
    public function testFormatCentsHundreds() {
        $this->assertSame(120, Affirm_Util::formatCents(1.20));
        $this->assertSame(123, Affirm_Util::formatCents(1.23));
        $this->assertSame(-123, Affirm_Util::formatCents(-1.23));
    }

    // 10^3
    public function testFormatCentsThousands() {
        $this->assertSame(1230, Affirm_Util::formatCents(12.30));
        $this->assertSame(1234, Affirm_Util::formatCents(12.34));
        $this->assertSame(-1234, Affirm_Util::formatCents(-12.34));
    }

    // 10^4
    public function testFormatCentsTensOfThousands() {
        $this->assertSame(12340, Affirm_Util::formatCents(123.40));
        $this->assertSame(12345, Affirm_Util::formatCents(123.45));
        $this->assertSame(-12345, Affirm_Util::formatCents(-123.45));
    }

    // 10^5
    public function testFormatCentsHundredThousands() {
        $this->assertSame(123450, Affirm_Util::formatCents(1234.50));
        $this->assertSame(123456, Affirm_Util::formatCents(1234.56));
        $this->assertSame(-123456, Affirm_Util::formatCents(-1234.56));
    }
}
