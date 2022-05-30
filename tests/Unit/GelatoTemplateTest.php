<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\PrintSuppliers\Controllers\GelatoTemplate;
use Illuminate\Support\Facades\DB;


class GelatoTemplateTest extends TestCase
{
    /**
     * @test
     * @dataProvider provider
     */
    public function get_template($input, $output)
    {

        $row = DB::table('print_suplliers_gelato_template')->first();
        var_dump([
            [
                [$row->category_id, $row->color, $row->size, $row->type, $row->style],
                [$row->product_uid]
            ]
        ]);
        die;

        $printifyTemplate = new GelatoTemplate;
        $response = $printifyTemplate->getTemplate($input);
        if ($response) {
            $this->assertSame($output, $response);
        }

    }

    public function provider () {
        $row = DB::table('print_suplliers_gelato_template')->first();
        return [];
        return [
            [
                [$row->category_id, $row->color, $row->size, $row->type, $row->style],
                [$row->product_uid]
            ]
        ];
        //
        // $row = DB::table('print_suplliers_gelato_template')->first();
        // print_r($row->category_id); die;
        // return [
        //     [[$row->category_id], [$row->product_uid]],
        // ];
        //
        //
        // return [
        //     [
        //         ["7", "Schwarz", "L", "Herren", NULL, "back", "de"],
        //         ["apparel_product_gca_t-shirt_gsc_crewneck_gcu_unisex_gqa_classic_gsi_l_gco_black_gpr_4-0"]
        //     ],
        // ];
    }
}
