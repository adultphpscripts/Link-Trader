<?php
session_start();
extract($_SERVER);
class editor {

    const PIN_CODE = '12345';
    private $LIBRARY_FILE = __DIR__ . '/library.dat';
    const _CHR_ = '$';
    public $URL = 'https://' . $HTTP_HOST . $REQUEST_URI;
    
    function get_code($class, $method) {
        if ($class == null || $method == null) { return ''; }
        $json = json_decode(file_get_contents($this->LIBRARY_FILE), TRUE);
        return $json[$class][$method];
    }
    function get_methods($class) {
        if ($class == null) { return ''; }
        $response = '';
        $json = json_decode(file_get_contents($this->LIBRARY_FILE), TRUE);
        foreach ($json[$class] as $method => $code) {
            $response.='<option value="' . $method . '">';
        }
        return $response;
    }
    
    function get_classes() {
        $response = '';
        $json = json_decode(file_get_contents($this->LIBRARY_FILE), TRUE);
        foreach ($json as $class => $nxt) {
            $response.='<option value="' . $class . '">';
        }
        return $response;
    }

    function save_data() {
        $data = '';
        $json = json_decode(file_get_contents($this->LIBRARY_FILE), TRUE);
        foreach ($json as $class => $nxt) {
            $data.='class ' . $class . ' extends library { ' . PHP_EOL;
            foreach ($nxt as $method => $code) {
                $data.='function ' . $method . ' (' . self::_CHR_ . 'vars) {' . PHP_EOL;
                $data.=$code . PHP_EOL;
                $data.='}' . PHP_EOL;
            }
            $data.='}' . PHP_EOL;
        }
        file_put_contents(__DIR__.'/library.code', $data);
    }

    function editor() {
        echo '<form action="'.$this->URL.'" method="post">
        <table width="100%">
            <tr>
                <td>
                <label for="class">Class</label>
                <input list="classes" id="class" name="class" value="' . $_POST["class"] . '">
                <datalist id="classes">
                ' . $this->get_classes() . ' 
                </datalist>
                </td>
                <td>
                <label for="class">Function</label>
                <input list="methods" id="method" name="method" value="' . $_POST["method"] . '">
                <datalist id="methods">
                ' . $this->get_methods($_POST["class"]) . ' 
                </datalist>
                </td>
                <td>
                <td>
                    <label for="save">    
                    <input type="radio" id="save" name="action" value="save" checked>
                        Save
                    </label>
                </td>
                <td>
                    <label for="delete">    
                    <input type="radio" id="delete" name="action" value="Delete">
                        Delete
                    </label>
                </td>
                <td
                    <input type="submit">
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <textarea id="code" name="code" style="width: 100%; height: 500px;"></textarea>
                </td>
            </tr>
        </table>
        </form>';
    }

    function signin() {
        if ($_SESSION["editor_login"] !==TRUE) {
            return TRUE;
        }elseif ($_POST["action"] == self::PIN_CODE) {
            $_SERVER["editor_login"] = TRUE;
            return TRUE;
        }
        echo '<form action="' . $this->URL . '" method="post">
        <input type="password" name="PIN_CODE">
        <input type="submit" name="action" value="login">
        </form>';
        return FALSE;
    }

    function __construct() {
        if ($this->signin()==FALSE) { die(); }
    }
}