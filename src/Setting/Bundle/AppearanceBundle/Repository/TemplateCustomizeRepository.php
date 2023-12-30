<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 10/9/15
 * Time: 8:05 AM
 */

namespace Setting\Bundle\AppearanceBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize;

class TemplateCustomizeRepository extends EntityRepository {



    public function updateTemplateCustomize(TemplateCustomize $entity , $data , $file){

        $em = $this->_em;

        $this->fileUploader($entity, $file);

        if(isset($data['logoDisplayWebsite']) and $data['logoDisplayWebsite'] != '') {
            $entity->setLogoDisplayWebsite($data['logoDisplayWebsite']);
        }
        if(isset($data['siteNameColor']) and $data['siteNameColor'] != '') {
            $entity->setSiteNameColor($data['siteNameColor']);
        }
        if(isset($data['siteBgColor']) and $data['siteBgColor'] != '') {
            $entity->setSiteBgColor($data['siteBgColor']);
        }
        if(isset($data['headerBgColor']) and $data['headerBgColor'] != '') {
            $entity->setHeaderBgColor($data['headerBgColor']);
        }
        if(isset($data['menuBgColor']) and $data['menuBgColor'] != '') {
            $entity->setMenuBgColor($data['menuBgColor']);
        }
        if(isset($data['menuLia']) and $data['menuLia'] != '') {
            $entity->setMenuLia($data['menuLia']);
        }

        if(isset($data['menuLiHovera']) and $data['menuLiHovera'] != '') {
            $entity->setMenuLiHovera($data['menuLiHovera']);
        }
        if(isset($data['bodyColor']) and $data['bodyColor'] != '') {
            $entity->setBodyColor($data['bodyColor']);
        }
        if(isset($data['footerBgColor']) and $data['footerBgColor'] != '') {
            $entity->setFooterBgColor($data['footerBgColor']);
        }
        if(isset($data['footerTextColor']) and $data['footerTextColor'] != '') {
            $entity->setFooterTextColor($data['footerTextColor']);
        }

        $em->persist($entity);
        $em->flush();
    }


    public function resize($newWidth, $targetFile, $originalFile) {

        $info = getimagesize($originalFile);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $new_image_ext = 'jpg';
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                $new_image_ext = 'png';
                break;

            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
                $new_image_ext = 'gif';
                break;

            default:
                throw new Exception('Unknown image type.');
        }

        $img = $image_create_func($originalFile);
        list($width, $height) = getimagesize($originalFile);

        $newHeight = ($height / $width) * $newWidth;
        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if (file_exists($targetFile)) {
            unlink($targetFile);
        }
        $image_save_func($tmp, "$targetFile.$new_image_ext");
    }

    public function fileUploader($entity, $file = '')
    {
        $em = $this->_em;
        /* @var $entity TemplateCustomize */
        if(isset($file['logoFile'])){
             $img = $file['logoFile'];
             $fileName = $img->getClientOriginalName();
             $imgName =  uniqid(). '.' .$fileName;
             $img->move($entity->getUploadDir(), $imgName);
             $entity->setLogo($imgName);
        }

        if(isset($file['androidLogoFile'])){

             $img = $file['androidLogoFile'];
             $fileName = $img->getClientOriginalName();
             $imgName =  uniqid(). '.' .$fileName;
             $img->move($entity->getUploadDir(), $imgName);
             $entity->setAndroidLogo($imgName);
        }

        if(isset($file['faviconFile'])){

             $img = $file['faviconFile'];
             $fileName = $img->getClientOriginalName();
             $imgName =  uniqid(). '.' .$fileName;
             $img->move($entity->getUploadDir(), $imgName);
             $entity->setFavicon($imgName);

        }

        if(isset($file['headerBgImage'])){

             $img = $file['headerBgImage'];
             $fileName = $img->getClientOriginalName();
             $imgName =  uniqid(). '.' .$fileName;
             $img->move($entity->getUploadDir(), $imgName);
             $entity->setHeaderBgImage($imgName);
        }

        if(isset($file['bgImage'])){

             $img = $file['bgImage'];
             $fileName = $img->getClientOriginalName();
             $imgName =  uniqid(). '.' .$fileName;
             $img->move($entity->getUploadDir(), $imgName);
             $entity->setBgImage($imgName);
        }
        $em->persist($entity);
        $em->flush();
    }
} 