<?php

namespace Tests;

use App\Entities\Media;
use App\Entities\User;
use org\bovigo\vfs\vfsStream;

class MediaTest extends DatabaseTestCase
{
    protected static $uploadPath = "";

    public static function setUpBeforeClass()
    {
        self::$uploadPath = vfsStream::setup()->url() . "/";
    }

    function setUp()
    {
        parent::setUp();
        $this->config->set("upload_path", self::$uploadPath);

        $media = $this->mediaRepo->get(1);
        file_put_contents(self::$uploadPath . $media->filename, "look: shiny pixels !");
    }

    function testGet()
    {
        $media = $this->mediaRepo->get(1);
        $this->assertInstanceOf(Media::class, $media);
        $this->assertSame(1, $media->id);
        $this->assertSame("the-picture", $media->slug);
        $this->assertFileExists(self::$uploadPath . $media->filename);

        $user = $media->getUser();
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);
    }

    function testUpload()
    {
        $imgPath = self::$uploadPath . "/the-file-name.png";

        file_put_contents($imgPath, "a text file");
        $_FILES["upload_file"] = [
            "tmp_name" => $imgPath, // wrong MIME type
            "name" => "the-file-name.png"
        ];
        $this->assertFalse($this->mediaRepo->upload("the-file-slug"));
        $errors = $this->session->getErrors();
        $this->assertContains("file.wrongextensionormimetype", $errors);

        imagepng(imagecreatetruecolor(1, 1), $imgPath);
        $_FILES["upload_file"] = [
            "tmp_name" => $imgPath,
            "name" => "the-file-name.exe" // wrong extension
        ];
        $this->assertFalse($this->mediaRepo->upload("the-file-slug"));
        $errors = $this->session->getErrors();
        $this->assertContains("file.wrongextensionormimetype", $errors);

        // note: can't test a successful upload because of move_uploaded_file() checking if the file was actually uploaded
        $_FILES["upload_file"] = [
            "tmp_name" => $imgPath,
            "name" => "the-file-name.png"
        ];
        $this->assertSame(self::$uploadPath, $this->config->get("upload_path"));
        $this->assertFileExists($imgPath);
        $this->assertFalse($this->mediaRepo->upload("the-file-slug"));
        $errors = $this->session->getErrors();
        $this->assertContains("file.errormovinguploadedfile", $errors);
    }

    function testUpdate()
    {
        $media = $this->mediaRepo->get(1);
        $newMedia = [
            "slug" => "the-new-picture",
            "filename" => "newfile name that will be ignored",
        ];

        $this->assertTrue($media->update($newMedia));
        $this->assertSame("the-new-picture", $media->slug);
        $this->assertSame("the-picture-the-original-filename-2018-01-01.jpeg", $media->filename);

        $media = $this->mediaRepo->get(1);
        $this->assertSame("the-new-picture", $media->slug);
        $this->assertSame("the-picture-the-original-filename-2018-01-01.jpeg", $media->filename);
    }

    function testDelete()
    {
        $media = $this->mediaRepo->get(1);
        $path = self::$uploadPath . "/" .$media->filename;
        $this->assertFileExists($path);

        $this->assertTrue($media->delete());
        $this->assertTrue($media->isDeleted);
        $this->assertFileNotExists($path);
    }
}
