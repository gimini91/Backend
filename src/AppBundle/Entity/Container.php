<?php
/**
 * Created by PhpStorm.
 * User: Leon
 * Date: 07.11.2017
 * Time: 15:14
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Swagger\Annotations as OAS;
use JMS\Serializer\Annotation as JMS;


/**
 * Class Container
 * @package AppBundle\Entity
 * @ORM\Table(name="containers")
 * @UniqueEntity("ipv4")
 * @UniqueEntity("ipv6")
 * @UniqueEntity("domainName")
 * @UniqueEntity("name")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContainerRepository")
 *
 * @OAS\Schema(schema="container", type="object")
 */
class Container
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @OAS\Property(example="14")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\Ip
     * @Assert\Type(type="string")
     * @OAS\Property(example="192.168.178.20")
     * @var string
     */
    protected $ipv4;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\Ip(version = 6)
     *
     * @Assert\Type(type="string")
     * @OAS\Property(example="fe80::20")
     * @var string
     */
    protected $ipv6;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\Regex("/[.]/")
     * @Assert\Type(type="string")
     * @OAS\Property(example="container14.localnet.com")
     * @var string
     */
    protected $domainName;

    /**
     * @ORM\Column(type="text")
     * @Assert\Type(type="string")
     * @OAS\Property(example="WebServer1")
     * @var string
     */
    protected $name;


    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $settings;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(type="string")
     * @OAS\Property(example="TODO Settings")
     * @var string
     */
    protected $state;

    /**
     * @ORM\ManyToOne(targetEntity="Host", inversedBy="containers")
     * @ORM\JoinColumn(name="host_id", referencedColumnName="id")
     *
     * @OAS\Property(ref="#/components/schemas/host")
     *
     * @JMS\Exclude()
     */
    protected $host;

    /**
     * @ORM\OneToMany(targetEntity="ContainerStatus", mappedBy="container")
     * @JMS\Exclude()
     */
    protected $statuses;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Profile", mappedBy="containers")
     * @JMS\Exclude()
     */
    protected $profiles;

    /**
     * @var BackupSchedule
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BackupSchedule", mappedBy="containers")
     * @JMS\Exclude()
     */
    protected $backupSchedules;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Backup", mappedBy="containers")
     * @JMS\Exclude()
     */
    protected $backups;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", inversedBy="containers")
     * @var Image
     */
    protected $image;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
        $this->statuses = new ArrayCollection();
        $this->backupSchedules = new ArrayCollection();
        $this->backups = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() :int
    {
        return $this->id;
    }

    /**
     * @param ContainerStatus $containerStatus
     */
    public function addStatus(ContainerStatus $containerStatus){
        if(!$this->statuses->contains($containerStatus)){
            $containerStatus->setContainer($this);
            $this->statuses->add($containerStatus);
        }
    }

    /**
     * @param ContainerStatus $containerStatus
     */
    public function removeStatus(ContainerStatus $containerStatus){
        if(!$this->statuses->contains($containerStatus)){
            $containerStatus->setContainer(null);
            $this->statuses->remove($containerStatus);
        }
    }

    /**
     * @return string | null
     */
    public function getIpv4() : ?string
    {
        return $this->ipv4;
    }

    /**
     * @return string | null
     */
    public function getIpv6() : ?string
    {
        return $this->ipv6;
    }

    /**
     * @param mixed $ipv4
     */
    public function setIpv4($ipv4)
    {
        $this->ipv4 = $ipv4;
    }

    /**
     * @param mixed $ipv6
     */
    public function setIpv6($ipv6)
    {
        $this->ipv6 = $ipv6;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param mixed $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return Host | null
     */
    public function getHost() : Host
    {
        return $this->host;
    }

    /**
     * @return ArrayCollection
     */
    public function getStatuses() :PersistentCollection
    {
        return $this->statuses;
    }

    /**
     * @param Host $host
     */
    public function setHost(Host $host)
    {
        $this->host = $host;
    }

    /**
     * @return string | null
     */
    public function getDomainName() : ?string
    {
        return $this->domainName;
    }

    /**
     * @param mixed $domainName
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    }

    /**
     * @return string
     */
    public function getState() : string
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage(Image $image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @param mixed $profiles
     */
    public function setProfiles($profiles): void
    {
        $this->profiles = $profiles;
    }



    /**
     * Checks if the container has at least on URI
     *
     *
     *
     * @return boolean
     */
    public function hasUri(){
        if($this->ipv4 || $this->ipv6 || $this->domainName)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Profile $profile
     */
    public function addProfile(Profile $profile){
        if ($this->profiles->contains($profile)) {
            return;
        }
        $this->profiles->add($profile);
        $profile->addContainer($this);
    }

    /**
     * @param Profile $profile
     */
    public function removeProfile(Profile $profile){
        if (!$this->profiles->contains($profile)) {
            return;
        }
        $this->profiles->removeElement($profile);
        $profile->removeContainer($this);
    }

    /**
     * @param BackupSchedule $backupSchedule
     */
    public function addBackupSchedule(BackupSchedule $backupSchedule){
        if ($this->backupSchedules->contains($backupSchedule)) {
            return;
        }
        $this->backupSchedules->add($backupSchedule);
        $backupSchedule->addContainer($this);
    }

    /**
     * @param BackupSchedule $backupSchedule
     */
    public function removeBackupSchedule(BackupSchedule $backupSchedule){
        if (!$this->backupSchedules->contains($backupSchedule)) {
            return;
        }
        $this->backupSchedules->removeElement($backupSchedule);
        $backupSchedule->removeContainer($this);
    }

    /**
     * @param Backup $backup
     */
    public function addBackup(Backup $backup){
        if ($this->backups->contains($backup)) {
            return;
        }
        $this->backups->add($backup);
        $backup->addContainer($this);
    }

    /**
     * @param Backup $backup
     */
    public function removeBackup(Backup $backup){
        if (!$this->backups->contains($backup)) {
            return;
        }
        $this->backups->removeElement($backup);
        $backup->removeContainer($this);
    }

    /**
     * @return int
     *
     * @OAS\Property(property="host_id", example="1")
     *
     * @JMS\VirtualProperty()
     */
    public function getHostId(){
        $id = $this->host->getId();

        return $id;
    }


    /**
     * @return array
     *
     * @OAS\Property(property="profile_id", example="[1]")
     *
     * @JMS\VirtualProperty()
     */
    public function getProfileId(){
        $ids[] = null;

        if($this->profiles->isEmpty()){
            return $ids;
        }

        $this->profiles->first();
        do{
            $ids[] = $this->profiles->current()->getId();
        }while($this->profiles->next());

        return $ids;
    }

    /** @see \Serializable::serialize() */
    // public function serialize()
    // {
    //     return serialize(array(
    //         $this->id,
    //         $this->name,
    //         $this->ipv4,
    //         $this->ipv6,
    //         $this->settings,
    //         $this->host
    //     ));
    // }

    // /** @see \Serializable::unserialize() */
    // public function unserialize($serialized)
    // {
    //     list (
    //         $this->id,
    //         $this->name,
    //         $this->ipv4,
    //         $this->ipv6,
    //         $this->settings,
    //         $this->host
    //         ) = unserialize($serialized);
    // }
}