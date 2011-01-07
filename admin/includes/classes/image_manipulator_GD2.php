<?php
/* ----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 XT-Commerce (image_manipulator_GD2.php 950 2005-05-14)
   (C) 2006 Noxware, B. W. Masanek - Support for transparency, enhanced PNG & GIF processing

   Third Party contributions:
   class thumbnail - proportional thumbnails with manipulations by mark@teckis.com
   You find more great scripts and some information at www.teckis.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
class image_manipulation
  {
  var $effects_disabled = false; //DokuMan - 2011-01-06
  function image_manipulation($resource_file, $max_width, $max_height, $destination_file="", $compression=IMAGE_QUALITY, $transform="")
    {
    $this->a = $resource_file;  // image to be thumbnailed
    $this->c = $transform;
    $this->d = $destination_file;  // thumbnail saved to
    $this->e = $compression;  // compression ration for jpeg thumbnails
    $this->m = $max_width;
    $this->n = $max_height;
    $this->compile();
    if($this->c !== ""){
      $this->manipulate();
      $this->create();
    }
  }

  //BOF - DokuMan - 2011-01-06 - added imagecopyresampled_adv - support for transparent PNGs
  // Support for transparency, enhanced PNG & GIF processing
  function imagecopyresampled_adv($image_type, &$dest, $source, $d_x, $d_y, $s_x, $s_y, $d_w, $d_h, $s_w, $s_h)
  {
    switch ($image_type) {
    // Process GIF images
    case 1:
      $transcol = imagecolortransparent($source);
      $dest = imagecreate($d_w, $d_h);
      imagepalettecopy($dest, $source);
      imagefill($dest, 0, 0, $transcol);
      imagecolortransparent($dest, $transcol);
      return imagecopyresized($dest, $source, $d_x, $d_y, $s_x, $s_y, $d_w, $d_h, $s_w, $s_h);
      break;

    // Process PNG images
    case 3:
      $dest = imageCreateTrueColor($d_w, $d_h);
      imagealphablending($dest, false);
      imagesavealpha($dest, true);
      $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 0);
      //BOF - DokuMan - 2011-01-06 - imagecolortransparent much faster on big images
      //for ($x = 0; $x < $d_w; $x++) {
      //  for ($y = 0; $y < $d_h; $y++) {
      //    imageSetPixel($dest, $x, $y, $transparent);
      //  }
      //}
      imagecolortransparent($dest,$transparent);
      //EOF - DokuMan - 2011-01-06 - imagecolortransparent much faster on big images

      return imagecopyresampled($dest, $source, $d_x, $d_y, $s_x, $s_y, $d_w, $d_h, $s_w, $s_h);
      break;

    // Any other images
    default:
      $dest = imageCreateTrueColor($d_w, $d_h);
      return imagecopyresampled($dest, $source, $d_x, $d_y, $s_x, $s_y, $d_w, $d_h, $s_w, $s_h);
    }
  }
  //EOF - DokuMan - 2011-01-06 - added imagecopyresampled_adv - support for transparent PNGs

  function compile()
    {
    $this->h = getimagesize($this->a);
    if(is_array($this->h)){
      $this->i = $this->h[0];
      $this->j = $this->h[1];
      $this->k = $this->h[2];
      if($this->m == '0'){
         $this->z = ($this->j / $this->n);
         $this->m = ($this->i / $this->z);
      }
      $this->o = ($this->i / $this->m);
      $this->p = ($this->j / $this->n);
      $this->q = ($this->o > $this->p) ? $this->m : round($this->i / $this->p); // width
      $this->r = ($this->o > $this->p) ? round($this->j / $this->o) : $this->n; // height
      }
    $this->s = ($this->k < 4) ? ($this->k < 3) ? ($this->k < 2) ? ($this->k < 1) ? Null : imagecreatefromgif($this->a) : imagecreatefromjpeg($this->a) : imagecreatefrompng($this->a) : Null;
    if($this->s !== Null) {
      //BOF - DokuMan - 2011-01-06 - use new imagecopyresampled_adv here
      // Creates an new image: $this->t. $this->k is the image type.
      $this->u = $this->imagecopyresampled_adv($this->k, $this->t, $this->s, 0, 0, 0, 0, $this->q, $this->r, $this->i, $this->j);
      //EOF - DokuMan - 2011-01-06 - use new imagecopyresampled_adv here
      }
    }

  function hex2rgb($hex_value)
    {
    $this->decval = hexdec($hex_value);
    return $this->decval;
    }
  function bevel($edge_width=10, $light_colour="FFFFFF", $dark_colour="000000")
    {
    // Not working properly for PNG images, so skipping
    if ($this->effects_disabled || $this->k == 3) return; //DokuMan - 2011-01-06
    $this->edge = $edge_width;
    $this->dc = $dark_colour;
    $this->lc = $light_colour;
    $this->dr = $this->hex2rgb(substr($this->dc,0,2));
    $this->dg = $this->hex2rgb(substr($this->dc,2,2));
    $this->db = $this->hex2rgb(substr($this->dc,4,2));
    $this->lr = $this->hex2rgb(substr($this->lc,0,2));
    $this->lg = $this->hex2rgb(substr($this->lc,2,2));
    $this->lb = $this->hex2rgb(substr($this->lc,4,2));
    $this->dark = @imagecreate($this->q,$this->r);
    $this->nadir = @imagecolorallocate($this->dark,$this->dr,$this->dg,$this->db);
    $this->light = @imagecreate($this->q,$this->r);
    $this->zenith = @imagecolorallocate($this->light,$this->lr,$this->lg,$this->lb);
    for($this->pixel = 0; $this->pixel < $this->edge; $this->pixel++){
      $this->opac =  100 - (($this->pixel+1) * (100 / $this->edge));
      @ImageCopyMerge($this->t,$this->light,$this->pixel,$this->pixel,0,0,1,$this->r-(2*$this->pixel),$this->opac);
      @ImageCopyMerge($this->t,$this->light,$this->pixel-1,$this->pixel-1,0,0,$this->q-(2*$this->pixel),1,$this->opac);
      @ImageCopyMerge($this->t,$this->dark,$this->q-($this->pixel+1),$this->pixel,0,0,1,$this->r-(2*$this->pixel),max(0,$this->opac-10));
      @ImageCopyMerge($this->t,$this->dark,$this->pixel,$this->r-($this->pixel+1),0,0,$this->q-(2*$this->pixel),1,max(0,$this->opac-10));
      }
    @ImageDestroy($this->dark);
    @ImageDestroy($this->light);
    }
  function greyscale($rv=38, $gv=36, $bv=26)
    {
    //BOF - DokuMan - 2011-01-06
    // Not working properly for PNG & GIF images, so skipping
    if ($this->effects_disabled || $this->k == 3 || $this->k == 1) return;
    $this->bgc = $bg_colour;
    $this->br = $this->hex2rgb(substr($this->bgc, 0, 2));
    $this->bg = $this->hex2rgb(substr($this->bgc, 2, 2));
    $this->bb = $this->hex2rgb(substr($this->bgc, 4, 2));
    $this->dot = @ImageCreate(6, 6);
    $this->dot_base = @ImageColorAllocate($this->dot, $this->br, $this->bg, $this->bb);
    $this->zenitha = @ImageColorClosest($this->t, $this->br, $this->bg, $this->bb);
    for ($this->rad = 0; $this->rad<6.3; $this->rad+=0.005) {
      $this->xpos = floor(($this->q) + (sin($this->rad) * ($this->q))) / 2;
      $this->ypos = floor(($this->r) + (cos($this->rad) * ($this->r))) / 2;
      $this->xto = 0;
      if ($this->xpos >= ($this->q/2)) {
        $this->xto = $this->q;
      }
      @ImageCopyMerge($this->t, $this->dot, $this->xpos - 3, $this->ypos - 3, 0, 0, 6, 6, 30);
      @ImageCopyMerge($this->t, $this->dot, $this->xpos - 2, $this->ypos - 2, 0, 0, 4, 4, 30);
      @ImageCopyMerge($this->t, $this->dot, $this->xpos - 1, $this->ypos - 1, 0, 0, 2, 2, 30);
    //EOF - DokuMan - 2011-01-06
    $this->rv = $rv;
    $this->gv = $gv;
    $this->bv = $bv;
    $this->rt = $this->rv+$this->bv+$this->gv;
    $this->rr = ($this->rv == 0) ? 0 : 1/($this->rt/$this->rv);
    $this->br = ($this->bv == 0) ? 0 : 1/($this->rt/$this->bv);
    $this->gr = ($this->gv == 0) ? 0 : 1/($this->rt/$this->gv);
    for( $this->dy = 0; $this->dy <= $this->r; $this->dy++ ){
      for( $this->dx = 0; $this->dx <= $this->q; $this->dx++ ){
        $this->pxrgb = @imagecolorat($this->t, $this->dx, $this->dy);
        $this->rgb = @ImageColorsforIndex( $this->t, $this->pxrgb );
        $this->newcol = ($this->rr*$this->rgb['red'])+($this->br*$this->rgb['blue'])+($this->gr*$this->rgb['green']);
        $this->setcol = @ImageColorAllocate( $this->t, $this->newcol, $this->newcol, $this->newcol );
        @imagesetpixel( $this->t, $this->dx, $this->dy, $this->setcol );
        }
      }
    }
  //BOF - DokuMan - 2011-01-06
  }
  //EOF - DokuMan - 2011-01-06

  function ellipse($bg_colour="FFFFFF")
    {
    // Not working properly for PNG images, so skipping
    if ($this->effects_disabled || $this->k == 3) return; //DokuMan - 2011-01-06
    $this->bgc = $bg_colour;
    $this->br = $this->hex2rgb(substr($this->bgc,0,2));
    $this->bg = $this->hex2rgb(substr($this->bgc,2,2));
    $this->bb = $this->hex2rgb(substr($this->bgc,4,2));
    $this->dot = @ImageCreate(6,6);
    $this->dot_base = @ImageColorAllocate($this->dot, $this->br, $this->bg, $this->bb);
    $this->zenitha = @ImageColorClosest($this->t, $this->br, $this->bg, $this->bb);
    for($this->rad = 0;$this->rad<6.3;$this->rad+=0.005){
      $this->xpos = floor(($this->q)+(sin($this->rad)*($this->q)))/2;
      $this->ypos = floor(($this->r)+(cos($this->rad)*($this->r)))/2;
      $this->xto = 0;
      if($this->xpos >= ($this->q/2)){
        $this->xto = $this->q;
        }
      @ImageCopyMerge($this->t,$this->dot,$this->xpos-3,$this->ypos-3,0,0,6,6,30);
      @ImageCopyMerge($this->t,$this->dot,$this->xpos-2,$this->ypos-2,0,0,4,4,30);
      @ImageCopyMerge($this->t,$this->dot,$this->xpos-1,$this->ypos-1,0,0,2,2,30);
      @ImageLine($this->t,$this->xpos,($this->ypos),$this->xto,($this->ypos),$this->zenitha);
      }
    @ImageDestroy($this->dot);
    }
  function round_edges($edge_rad=3, $bg_colour="FFFFFF", $anti_alias=1)
    {
    // Not working properly for PNG images, so skipping
    if ($this->effects_disabled || $this->k == 3) return; //DokuMan - 2011-01-06
    $this->er = $edge_rad;
    $this->bgd = $bg_colour;
    $this->aa = min(3,$anti_alias);
    $this->br = $this->hex2rgb(substr($this->bgd,0,2));
    $this->bg = $this->hex2rgb(substr($this->bgd,2,2));
    $this->bb = $this->hex2rgb(substr($this->bgd,4,2));
    $this->dot = @ImageCreate(1,1);
    $this->dot_base = @ImageColorAllocate($this->dot, $this->br, $this->bg, $this->bb);
    $this->zenitha = @ImageColorClosest($this->t, $this->br, $this->bg, $this->bb);
    for($this->rr = 0-$this->er; $this->rr <= $this->er; $this->rr++){
      $this->ypos = ($this->rr < 0) ? $this->rr+$this->er-1 : $this->r-($this->er-$this->rr);
      for($this->cr = 0-$this->er; $this->cr <= $this->er; $this->cr++){
        $this->xpos = ($this->cr < 0) ? $this->cr+$this->er-1 : $this->q-($this->er-$this->cr);
        if($this->rr !== 0 || $this->cr !== 0){
          $this->d_dist = round(sqrt(($this->cr*$this->cr)+($this->rr*$this->rr)));
          $this->opaci = ($this->d_dist < $this->er-$this->aa) ? 0 : max(0, 100-(($this->er-$this->d_dist)*33));
          $this->opaci = ($this->d_dist > $this->er) ? 100 : $this->opaci;
          @ImageCopyMerge($this->t,$this->dot,$this->xpos,$this->ypos,0,0,1,1,$this->opaci);
          }
        }
      }
    @imagedestroy($this->dot);
    }
  function merge($merge_img="", $x_left=0, $y_top=0, $merge_opacity=70, $trans_colour="FF0000")
    {
    if ($this->effects_disabled) return; //DokuMan - 2011-01-06
    $this->mi = $merge_img;
    $this->xx = ($x_left < 0) ? $this->q+$x_left : $x_left;
    $this->yy = ($y_top < 0) ? $this->r+$y_top : $y_top;
    $this->mo = $merge_opacity;
    $this->tc = $trans_colour;
    $this->tr = $this->hex2rgb(substr($this->tc,0,2));
    $this->tg = $this->hex2rgb(substr($this->tc,2,2));
    $this->tb = $this->hex2rgb(substr($this->tc,4,2));
    $this->md = @getimagesize($this->mi);
    $this->mw = $this->md[0];
    $this->mh = $this->md[1];
    $this->mm = ($this->md[2] < 4) ? ($this->md[2] < 3) ? ($this->md[2] < 2) ? imagecreatefromgif($this->mi) : imagecreatefromjpeg($this->mi) : imagecreatefrompng($this->mi) : Null;
    for($this->ypo = 0; $this->ypo < $this->mh; $this->ypo++){
      for($this->xpo = 0; $this->xpo < $this->mw; $this->xpo++){
        $this->indx_ref = @imagecolorat($this->mm, $this->xpo, $this->ypo);
        $this->indx_rgb = @imagecolorsforindex($this->mm, $this->indx_ref);
        if(($this->indx_rgb['red'] == $this->tr) && ($this->indx_rgb['green'] == $this->tg) && ($this->indx_rgb['blue'] == $this->tb)){
          // transparent colour, so ignore merging this pixel

          } else {
          @imagecopymerge($this->t, $this->mm, $this->xx+$this->xpo, $this->yy+$this->ypo, $this->xpo, $this->ypo, 1, 1, $this->mo);
          }
        }
      }
    @imagedestroy($this->mm);
  }
  function frame($light_colour="FFFFFF", $dark_colour="000000", $mid_width=4, $frame_colour = "" )
    {
    if ($this->effects_disabled) return; //DokuMan - 2011-01-06
    $this->rw = $mid_width;
    $this->dh = $dark_colour;
    $this->lh = $light_colour;
    $this->frc = $frame_colour;
    $this->fr = $this->hex2rgb(substr($this->dh,0,2));
    $this->fg = $this->hex2rgb(substr($this->dh,2,2));
    $this->fb = $this->hex2rgb(substr($this->dh,4,2));
    $this->gr = $this->hex2rgb(substr($this->lh,0,2));
    $this->gg = $this->hex2rgb(substr($this->lh,2,2));
    $this->gb = $this->hex2rgb(substr($this->lh,4,2));
    $this->zen = @ImageColorClosest($this->t, $this->gr, $this->gg, $this->gb);
    $this->nad = @ImageColorClosest($this->t, $this->fr, $this->fg, $this->fb);
    $this->mid = ($this->frc == "") ? @ImageColorClosest($this->t, ($this->gr+$this->fr)/2, ($this->gg+$this->fg)/2, ($this->gb+$this->fb)/2) : ImageColorClosest($this->t, $this->hex2rgb(substr($this->frc,0,2)), $this->hex2rgb(substr($this->frc,2,2)), $this->hex2rgb(substr($this->frc,4,2)));
    @imageline($this->t, 0, 0, $this->q, 0, $this->zen);
    @imageline($this->t, 0, 0, 0, $this->r, $this->zen);
    @imageline($this->t, $this->q-1, 0, $this->q-1, $this->r, $this->nad);
    @imageline($this->t, 0, $this->r-1, $this->q, $this->r-1, $this->nad);
    @imageline($this->t, $this->rw+1, $this->r-($this->rw+2), $this->q-($this->rw+2), $this->r-($this->rw+2), $this->zen); // base in
    @imageline($this->t, $this->q-($this->rw+2), $this->rw+1, $this->q-($this->rw+2), $this->r-($this->rw+2), $this->zen); // base right
    @imageline($this->t, $this->rw+1, $this->rw+1, $this->q-($this->rw+1), $this->rw+1, $this->nad);
    @imageline($this->t, $this->rw+1, $this->rw+1, $this->rw+1, $this->r-($this->rw+1), $this->nad);
    for($this->crw = 0; $this->crw < $this->rw; $this->crw++){
      @imageline($this->t, $this->crw+1, $this->crw+1, $this->q-($this->crw+1), $this->crw+1, $this->mid); // top
      @imageline($this->t, $this->crw+1, $this->r-($this->crw+2), $this->q-($this->crw+1), $this->r-($this->crw+2), $this->mid); // base
      @imageline($this->t, $this->crw+1, $this->crw+1, $this->crw+1, $this->r-($this->crw+1), $this->mid); // left
      @imageline($this->t, $this->q-($this->crw+2), $this->crw, $this->q-($this->crw+2), $this->r-($this->crw+1), $this->mid); // right
      }
    }
  function drop_shadow($shadow_width, $shadow_colour="000000", $background_colour="FFFFFF")
    {
    // Not working properly for PNG & GIF images, so skipping
    if ($this->effects_disabled || $this->k == 3 || $this->k == 1) return; //DokuMan - 2011-01-06
    $this->sw = $shadow_width;
    $this->sc = $shadow_colour;
    $this->sbr = $background_colour;
    $this->sr = $this->hex2rgb(substr($this->sc,0,2));
    $this->sg = $this->hex2rgb(substr($this->sc,2,2));
    $this->sb = $this->hex2rgb(substr($this->sc,4,2));
    $this->sbrr = $this->hex2rgb(substr($this->sbr,0,2));
    $this->sbrg = $this->hex2rgb(substr($this->sbr,2,2));
    $this->sbrb = $this->hex2rgb(substr($this->sbr,4,2));
    $this->dot = @ImageCreate(1,1);
    $this->dotc = @ImageColorAllocate($this->dot, $this->sr, $this->sg, $this->sb);
    $this->v = @imagecreatetruecolor($this->q, $this->r);
    $this->sbc = @imagecolorallocate($this->v, $this->sbrr, $this->sbrg, $this->sbrb);
    $this->rsw = $this->q-$this->sw;
    $this->rsh = $this->r-$this->sw;
    @imagefill($this->v, 0, 0, $this->sbc);
    for($this->sws = 0; $this->sws < $this->sw; $this->sws++){
      $this->s_opac = max(0, 90-($this->sws*(100 / $this->sw)));
      for($this->sde = $this->sw; $this->sde < $this->rsh+$this->sws+1; $this->sde++){
        @imagecopymerge($this->v, $this->dot, $this->rsw+$this->sws, $this->sde, 0, 0, 1, 1, $this->s_opac);
        }
      for($this->bse = $this->sw; $this->bse < $this->rsw+$this->sws; $this->bse++){
        @imagecopymerge($this->v, $this->dot, $this->bse, $this->rsh+$this->sws, 0, 0, 1, 1, $this->s_opac);
        }
      }
    @imagecopyresampled($this->v, $this->t, 0, 0, 0, 0, $this->rsw, $this->rsh, $this->q, $this->r);
    @imagecopyresampled($this->t, $this->v, 0, 0, 0, 0, $this->q, $this->r, $this->q, $this->r);
    @imagedestroy($this->v);
    @imagedestroy($this->dot);
    }
  function motion_blur($num_blur_lines, $background_colour="FFFFFF")
    {
    // Not working properly for PNG images, so skipping
    if ($this->effects_disabled || $this->k == 3) return; //DokuMan - 2011-01-06
    $this->nbl = $num_blur_lines;
    $this->shw = ($this->nbl*2)+1;
    $this->bk = $background_colour;
    $this->kr = $this->hex2rgb(substr($this->bk,0,2));
    $this->kg = $this->hex2rgb(substr($this->bk,2,2));
    $this->kb = $this->hex2rgb(substr($this->bk,4,2));
    $this->w = @imagecreatetruecolor($this->q, $this->r);
    $this->shbc = @imagecolorallocate($this->w, $this->kr, $this->kg, $this->kb);
    $this->rsw = $this->q-$this->shw;
    $this->rsh = $this->r-$this->shw;
    @imagefill($this->w, 0, 0, $this->shbc);
    $this->rati = $this->r / $this->rsh;
    for($this->lst = 0; $this->lst < $this->nbl; $this->lst++){
      $this->opacit = max(0, 70-($this->lst*(85 / $this->nbl)));
      for($this->yst = 0; $this->yst < $this->rsh; $this->yst++){
        @imagecopymerge($this->w, $this->t, $this->rsw+(2*$this->lst)+1, $this->yst+(2*$this->lst)+2, $this->q-1, $this->yst*$this->rati, 1, 1, $this->opacit);
        }
      for($this->xst = 0; $this->xst < $this->rsw; $this->xst++){
        @imagecopymerge($this->w, $this->t, $this->xst+(2*$this->lst)+1, $this->rsh+(2*$this->lst)+1, $this->xst*$this->rati, $this->r-1, 1, 1, $this->opacit);
        }
      }
    @imagecopyresampled($this->w, $this->t, 0, 0, 0, 0, $this->rsw, $this->rsh, $this->q, $this->r);
    @imagecopyresampled($this->t, $this->w, 0, 0, 0, 0, $this->q, $this->r, $this->q, $this->r);
    @imagedestroy($this->w);
    }
  function manipulate()
    {
    if($this->c !== "" && $this->s !== Null){
      eval("\$this->maniparray = array(".$this->c.");");
      foreach($this->maniparray as $manip){
        eval("\$this->".$manip.";");
        }
      }
    }
  function create()
    {
    if($this->s !== Null){
      if($this->d !== ""){
        //BOF - DokuMan - 2011-01-06 - support jpg, gif and png
        ob_start();
        $image_type = $this->k;
        switch ($image_type) {
          case 1:
             // Keep transparent color
            $transcol = imagecolortransparent($this->s);
            imagecolortransparent($this->t, $transcol);
            imagegif($this->t, $this->d);
            break;

          // PNG image
          case 3:
            imagealphablending($this->t, true);
            imagesavealpha($this->t, true);
            imagepng($this->t, $this->d);
            break;

          // Other images
          default:
            imagejpeg($this->t, $this->d, $this->e);
        }
        ob_end_clean();
        //EOF - DokuMan - 2010-01-06 - support jpg, gif and png
      }
      imagedestroy($this->s);
      imagedestroy($this->t);
      }
    }
  }
?>