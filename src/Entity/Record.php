<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 19/08/17
 * Time: 22:36
 */

namespace SeanUk\Silex\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Record
 * @package SeanUk\Silex\Entity
 * @ORM\Entity()
 */
class Record
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
}