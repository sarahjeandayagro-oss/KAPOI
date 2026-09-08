<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proposal_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proposal_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('commenter_role')->nullable();
            $table->integer('page')->nullable();
            $table->text('location')->nullable();
            $table->text('comment_text');
            $table->timestamps();

            $table->index('proposal_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposal_comments');
    }
};
