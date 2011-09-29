<!--

Copyright (C) 2005 Daniel Green

This software is provided 'as-is', without any express or implied warranty. In no event will the author be held liable for any damages arising from the use of this software. 

Permission is granted to anyone to use this software for any purpose, including commercial applications, and to alter it and redistribute it freely, subject to the following restrictions:

1. The origin of this software must not be misrepresented; you must not claim that you wrote the original software. If you use this software in a product, an acknowledgment in the product documentation would be appreciated but is not required.

2. Altered source versions must be plainly marked as such, and must not be misrepresented as being the original software.

3. This notice may not be removed or altered from any source distribution.

-->

<?php
// thanks Kaboon!
ob_start("ob_gzhandler");

$url = isset($_GET['url']) ? $_GET['url'] : "./input.png";

if ( $url[0] == '.' && $url != "./input.png" )
  $url = "./input.png";

$str = isset($_GET['str']) ? $_GET['str'] : "W";
$str = stripslashes($str);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <title> imgtohtml </title>
    <style>
      body, table, tr, td, input
      {
        font-family: Monospace;
        font-size: 12px;
        /*line-height: 1;*/
      }
    </style>

      <?php

      if ( strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") )
      {
      ?>
    <style>
      #img
      {
        font-size: 1px;
        font-family: Monospace;
        line-height: 1.0;
      }
      </style>
      <?php
      }
      else
      {
      ?>
      <style>
      #img
      {
        font-size: 1px;
        font-family: Monospace;
        line-height: 0.5;
      }
    </style>
      <?php
      }
      ?>
  </head>

  <body>
    <table width="500">
      <tr>
        <td colspan=2 align="center">
          <span style="font-size: 8px;">the aptly-titled</span> <span style="font-size: 18px; font-weight: bold;">imgtohtml converter</span>
        </td>
      </tr>
      <tr>
        <td colspan=2><b>Introduction</b></td>
      </tr>
      <tr>
        <td colspan=2>
          This script converts both png and jpeg images into html.  Nothing fancy, but kinda cool for a little timewaster. =)<br /><br />
        </td>
      </tr>
      <tr>
        <td colspan=2>
          <b>Browser Support</b>
        </td>
      </tr>
      <tr>
        <td colspan=2>
          * Firefox (PC) - Fully supported<br />
          * Internet Explorer (PC) - <b>Broken</b><br />
          * Opera / Mozilla (PC / Mac?) - Untested<br />
          * Safari (Mac) - Same as IE (PC), see above<br />
          * Firefox (Mac) - Incomplete!<br />
          * Internet Explorer (Mac) - Who gives a fuck.<br /><br />
        </td>
      </tr>
      <tr>
        <td colspan=2><b>Advice</b></td>
      <tr>
      <tr>
        <td colspan=2>The <a href="imgtohtml.php">sample image</a>'s filesize is increased by 16.3 times (~6k -> ~98kb) when converted to html (using 'W' for each pixel), so be wary of the input images and pixel strings you choose.  Aside from wasting mindphone's glorious bandwidth, you'll probably end up crashing your browser.  <b>Pixel string choice matters.</b><br /><br /></td>
      </tr>
      <tr>
        <td align="center"><b>Image (PNG/JPG)</b></td>
        <td align="center"><b>HTML</b></td>
      </tr>
        <td valign="top" align="center"><img src="<?php print($url); ?>"></td>
        <td valign="top" align="center" id="img">
          <?php

          $ext = explode(".", $url);
          $ext = $ext[sizeof($ext) - 1];
          $ext = strtolower($ext);

          $input = 0;

          switch ( $ext )
          {
            case "jpg":
            case "jpeg":
            case "jpe":
              $input = ImageCreateFromJpeg($url);
            break;

            case "png":
              $input = ImageCreateFromPng($url);
            break;
          }

          if ( !$input )
          {
            print("<span style=\"font-size: 12px;\">Unsupported image format</span>");
          }
          else
          {            
            $strlen = strlen($str);
            $i = 0;
            $inspan = 0;
            $prevcolor = -1;

            for ( $y = 0; $y < ImageSY($input); $y++ )
            {
              for ( $x = 0; $x < ImageSX($input); $x++ )
              {
                $color = ImageColorAt($input, $x, $y);
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;

                $h1 = "" . dechex($r);
                $h2 = "" . dechex($g);
                $h3 = "" . dechex($b);

                if ( !strlen($h1) )
                  $h1 = "00";
                else if ( strlen($h1) == 1 )
                  $h1 = "0" . $h1;

                if ( !strlen($h2) )
                  $h2 = "00";
                else if ( strlen($h2) == 1 )
                  $h2 = "0" . $h2;

                if ( !strlen($h3) )
                  $h3 = "00";
                else if ( strlen($h3) == 1 )
                  $h3 = "0" . $h3;

                $hex = $h1 . $h2 . $h3;

                if ( $inspan && $prevcolor != $hex )
                {
                  $inspan = 0;
                  print("</span>");
                }

                if ( !$inspan )
                {
                  $inspan = 1;
                  print("<span style=\"color: #" . $hex . ";\">");
                }

                print($str[$i++ % $strlen]);

                $prevcolor = $hex;
              }

              if ( $inspan )
              {
                $inspan = 0;
                print("</span>"); // thanks Kaboon!
              }

              print("<br />\n");
            }
          }

          ?>
        </td>
      </tr>
      <tr>
        <td colspan=2 style="height: 10px;">&nbsp;</td>
      </tr>
      <tr>
        <td colspan=2>
          <form method="GET" action="imgtohtml.php">
            <table cellpadding=0 cellspacing=0 border=0>
              <tr>
                <td colspan=2><b>Settings</b></td>
              <tr>
                <td width=100>Pixel string:</td><td width=200><input name="str" id="str" value="<?php print(($str)); ?>"></td>
                <td rowspan=2 valign="middle" align="left"><input type="submit" value="Get Image"></td>
              </tr>
              <tr>
                <td width=100>URL to png:</td><td><input name="url" value="<?php print($url); ?>"></td>
              </tr>
            </table>
          </form>
        </td>
      </tr>
      <tr>
        <td colspan=2><br /><b>Fun / Possibly Interesting Choices</b></td>
      </tr>
      <tr>
        <td colspan=2><a href="?url=http%3A%2F%2Fwww.h6.dion.ne.jp%2F%7Ekoske%2Fukagaka%2Fsaimoe%2Fmagic_wand_tool.png">Anime or something</a><br />
                               <a href="?str=W&url=http%3A%2F%2Fus.movies1.yimg.com%2Fmovies.yahoo.com%2Fimages%2Fhv%2Fphoto%2Fmovie_pix%2Fwarner_brothers%2Fconstantine%2Fmaynard_james_keenan%2Fconstantinepred.jpg">Yeehaw</a><br />
                               <a href="?str=W&url=http%3A%2F%2Fastrogeology.usgs.gov%2Fassets%2Fwallpaper%2Fsun.jpg">Hot hot hot</a></td>
      </tr>
      <tr>
        <td colspan=2><br /><b>Source code</b></td>
      </tr>
      <tr>
        <td colspan=2><a href="imgtohtml.phps">Get it!</a></td>
      </tr>
      <tr>
        <td colspan=2><br /><b>Changelog-ish thing</b></td>
      </tr>
      <tr>
        <td colspan=2>
            * 08/18/05 - Added gzip compression (thanks Kaboon)<br />
            * 08/18/05 - Minor bugfix: a /span was missing the &gt; (thanks Kaboon)<br />
            <br />
            * 08/16/05 - Added source code link!<br />
            * 08/16/05 - Prettified page somewhat (added samples too heh)<br />
            * 08/16/05 - Added jpeg support<br />
            * 08/16/05 - Added customizable pixel string<br />
            <br />
            * 08/15/05 - Added customizable image location<br />
            * 08/15/05 - Optimized filesize by combining same color pixels<br />
            * 08/15/05 - Added IE support (seems fucked atm tho)<br />
            <br />
            * 08/14/05 - Initial release
        </td>
      </tr>
      <tr>
        <td colspan=2 align="center"><br />Written by and Copyright &copy; 2005 <a href="http://antimac.org">Daniel Green</a></td>
      </tr>
    </table>
  </body>
</html>
