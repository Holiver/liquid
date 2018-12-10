<?php
namespace Liquid\Object;

use Liquid\Drop;

class Template extends Drop {

    private $path;
    private $name;
    private $directory;
    private $suffix;
    private $type;

    public function __construct($path, $type='') {
        $this->path = $path;
        $path_pos = strrpos($this->path, '/');
        if ($path_pos !== false) {
            $this->directory = substr($this->path, 0, $path_pos);
            $basename = substr($this->path, $path_pos + 1);
        } else {
            $this->directory = '';
            $basename = $path;
        }

        $name_pos = strpos($basename, '.');
        if ($name_pos !== false) {
            $this->name = substr($basename, 0, $name_pos);
            $this->suffix = substr($basename, $name_pos + 1);
        } else {
            $this->name = $basename;
            $this->suffix = '';
        }

        $this->type = $type;
    }

    public function name() {
        return $this->name;
    }

    public function directory() {
        return $this->directory;
    }

    public function suffix() {
        return $this->suffix;
    }

    public function type() {
        return $this->type;
    }

    public function __toString() {
        return $this->name();
    }

    public function __toJson() {
        return $this->name();
    }

}