<?php

namespace ZhanghengDread\Summernote;

use Encore\Admin\Form\Field;

class Editor extends Field
{
    protected $view = 'laravel-admin-summernote::editor';

    protected static $css = [
        'vendor/laravel-admin-ext/summernote/dist/summernote.css',
    ];

    protected static $js = [
        'vendor/laravel-admin-ext/summernote/dist/summernote.min.js',
    ];

    public function render()
    {
        $uploadUrl = Summernote1::config('image_upload_url', '/api/summernote_upload_image');
        $name = $this->formatName($this->column);

        $config = (array)Summernote1::config('config');

        $config = json_encode(array_merge([
            'height' => 300,
        ], $config));


        $this->script = <<<EOT
        var config = $config;
        console.log(config)
        
        config.callbacks = {
            onImageUpload: function(files) {
                console.log(files)
                var file = files[0];
                sendFile(file, file.name);
                }
        };
        
        var editor = $('#{$this->id}');

        editor.summernote(config);

        editor.on("summernote.change", function (e) {
            var html = $('#{$this->id}').summernote('code');
            $('input[name=$name]').val(html);
        });



        function sendFile(file, filename) {
            data = new FormData();
            data.append("file", file);
            $.ajax({
                data: data,
                type: "POST",
                url: ""{$uploadUrl}"",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                console.log(url)
                editor.summernote('insertImage', url, filename);
                }
            });
        }

EOT;

        return static::parentRender();
    }

    protected function parentRender()
    {
        if (!$this->shouldRender()) {
            return '';
        }

        \Admin::script($this->script);

        return view($this->getView(), $this->variables());
    }
}
