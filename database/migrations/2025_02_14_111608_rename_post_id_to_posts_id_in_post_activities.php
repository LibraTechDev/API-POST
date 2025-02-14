<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('post_activities', function (Blueprint $table) {
            // Hapus foreign key lama (jika ada)
            $table->dropForeign(['post_id']);

            // Ubah nama kolom
            $table->renameColumn('post_id', 'posts_id');

            // Tambahkan foreign key baru
            $table->foreign('posts_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('post_activities', function (Blueprint $table) {
            // Hapus foreign key baru
            $table->dropForeign(['posts_id']);

            // Ubah kembali nama kolom
            $table->renameColumn('posts_id', 'post_id');

            // Tambahkan kembali foreign key lama
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }
};