<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/31
 * Time: 15:40
 */

?>

<form action="/weixin/toilet/upload" name="pic"  method="post"  enctype="multipart/form-data">
    <table>
        <tr>  <td> 图片一 : <input id="pic1" name="pic1" type="file" onchange="pic1()"/> </td>  </tr>
    </table>
    <input class="btn btn-primary" value="提交" type="submit" />
</form>