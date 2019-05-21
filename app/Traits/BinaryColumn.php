<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait BinaryColumn
{

    public function createBinary16Column($table, $column, 
        $isPrimary = TRUE, $createIndex = TRUE, $isNullable = FALSE)
    {
        if($isNullable)
        {
            DB::statement("ALTER TABLE $table ADD $column BINARY(16) NULL FIRST;");
        }
        else
        {
            DB::statement("ALTER TABLE $table ADD $column BINARY(16) NOT NULL FIRST;");
        }

        if($isPrimary)
        {
            DB::statement("ALTER TABLE $table ADD PRIMARY KEY ($column);");
        }

        if($createIndex)
        {
            $index = $table.'_'.$column;
            DB::statement("CREATE UNIQUE INDEX $index on $table ($column);");
        }
    }

    public function setForeignKey($nativeTable, $nativeColumn, $foreignTable, $foreignColumn)
    {
        Schema::table($nativeTable, 
            function( Blueprint $table ) use ($nativeColumn, $foreignTable, $foreignColumn)
            {
                $table->foreign($nativeColumn)
                      ->references($foreignColumn)
                      ->on($foreignTable)
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
    }

}
