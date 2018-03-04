<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BackupSchedule
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 */
class BackupSchedule
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     * @Assert\NotNull
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    protected $name;

    /**
     * @var string | null
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Type("string")
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\Choice({"daily", "weekly", "monthly"})
     */
    protected $executionTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\Type("string")
     */
    protected $destination;

    /**
     * @var Container
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Container", inversedBy="backup_schedule")
     * @ORM\JoinTable(
     *  joinColumns={
     *      @ORM\JoinColumn(name="backup_schedule_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="container_id", referencedColumnName="id")
     *  }
     * )
     *
     * @JMS\Exclude()
     */
    protected $containers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Backup", mappedBy="backupSchedule")
     * @JMS\Exclude()
     */
    protected $backups;

    /**
     * BackupSchedule constructor.
     */
    public function __construct()
    {
        $this->containers = new ArrayCollection();
        $this->backups = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getExecutionTime(): string
    {
        return $this->executionTime;
    }

    /**
     * @param string $executionTime
     */
    public function setExecutionTime($executionTime): void
    {
        $this->executionTime = $executionTime;
    }

    /**
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     */
    public function setDestination($destination): void
    {
        $this->destination = $destination;
    }

    /**
     * @return PersistentCollection
     */
    public function getContainers(): PersistentCollection
    {
        return $this->containers;
    }

    /**
     * @param Container $container
     */
    public function addContainer(Container $container)
    {
        if ($this->containers->contains($container)) {
            return;
        }
        $this->containers->add($container);
        $container->addBackupSchedule($this);
    }

    /**
     * @param Container $container
     */
    public function removeContainer(Container $container){
        if (!$this->containers->contains($container)) {
            return;
        }
        $this->containers->removeElement($container);
        $container->removeBackupSchedule($this);
    }

    /**
     * @return array
     *
     * @JMS\VirtualProperty()
     */
    public function getContainerId(){
        $ids[] = null;

        if($this->containers->isEmpty()){
            return $ids;
        }

        $this->containers->first();
        do{
            $ids[] = $this->containers->current()->getId();
        }while($this->containers->next());

        return $ids;
    }

    /**
     * Adds a successful Backup to the BackupSchedule.
     * @param Backup $backup
     */
    public function addBackup(Backup $backup){
        if ($this->backups->contains($backup)) {
            return;
        }
        $this->backups->add($backup);
        $backup->setBackupSchedule($this);
    }

    /**
     * Removes a successful Backup from the BackupSchedule.
     * @param Backup $backup
     */
    public function removeBackup(Backup $backup){
        if (!$this->backups->contains($backup)) {
            return;
        }
        $this->backups->removeElement($backup);
        $backup->setBackupSchedule(null);
    }
}