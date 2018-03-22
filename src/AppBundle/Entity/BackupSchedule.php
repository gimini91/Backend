<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as OAS;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\BackupDestination;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Class BackupSchedule
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @OAS\Schema(schema="backupSchedule", type="object")
 * @UniqueEntity("name")
 * @UniqueEntity("token")
 */
class BackupSchedule
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OAS\Property(example="14")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     * @Assert\NotNull
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @OAS\Property(example="Schedule1")
     */
    protected $name;

    /**
     * @var string | null
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Type("string")
     * @OAS\Property(example="Schedule1 des")
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\Choice({"daily", "weekly", "monthly"})
     * @Assert\Type("string")
     * @OAS\Property(example="daily")
     */
    protected $executionTime;

    /**
     * @var BackupDestination
     *
     * @ORM\ManyToOne(targetEntity="BackupDestination", inversedBy="backupSchedules")
     * @ORM\JoinColumn(name="destination_id", referencedColumnName="id")
     * @Assert\NotNull
     */
    protected $destination;

    /**
     * whether to do a full or incremental backup
     *
     *
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\Type("string")
     * @Assert\Choice({"full", "incremental"})
     * @OAS\Property(example="full")
     */
    protected $type;

    /**
     * token used for authorization of the backup script on the Host
     * also used to find the association between backup and the backup schedule
     *
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     *
     * @Assert\Type("string")
     * @JMS\Exclude()
     *
     */
    protected $token;

    /**
     * webhook to generate a backup
     *
     * @var string
     * @ORM\Column(type="string", unique=true)
     * @Assert\Type("string")
     */
    protected $webhookUrl;

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
        $this->token = bin2hex(random_bytes(10));
        $this->containers = new ArrayCollection();
        $this->backups = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) : void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getDescription() : ? string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription($description) : void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getExecutionTime() : string
    {
        return $this->executionTime;
    }

    /**
     * @param string $executionTime
     */
    public function setExecutionTime($executionTime) : void
    {
        $this->executionTime = $executionTime;
    }

    /**
     * @return BackupDestination
     */
    public function getDestination() : BackupDestination
    {
        return $this->destination;
    }

    /**
     * @param BackupDestination $destination
     */
    public function setDestination($destination) : void
    {
        $this->destination = $destination;
    }

    /**
     * Get whether to do a full or incremental backup
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set whether to do a full or incremental backup
     *
     * @param  string  $type  whether to do a full or incremental backup
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return PersistentCollection
     */
    public function getContainers() : PersistentCollection
    {
        return $this->containers;
    }

    /**
     * set Containers
     *
     * @param PersitentCollection $containers
     * @return void
     */
    public function setContainers($containers)
    {
        $this->containers = $containers;
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
    public function removeContainer(Container $container)
    {
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
    public function getContainerId()
    {
        $ids[] = null;

        if ($this->containers->isEmpty()) {
            return $ids;
        }

        $this->containers->first();
        do {
            $ids[] = $this->containers->current()->getId();
        } while ($this->containers->next());

        return $ids;
    }

    /**
     * Adds a successful Backup to the BackupSchedule.
     * @param Backup $backup
     */
    public function addBackup(Backup $backup)
    {
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
    public function removeBackup(Backup $backup)
    {
        if (!$this->backups->contains($backup)) {
            return;
        }
        $this->backups->removeElement($backup);
        $backup->setBackupSchedule(null);
    }


    /**
     * Returns the Commands which will be written in a shell script.
     *
     * @return string
     */
    public function getShellCommands()
    {
        $commandTexts = '#!/bin/sh \n \n';


        foreach ($this->containers as $container) {
            $commandTexts = $commandTexts . '
                # Backup for Container ' . $container->getName() . ' to ' . $this->destination->getName() . '\n
                \n
                DIRECTORY = /tmp/' . $this->name . '/ \n
                # Just generating a random number \n
                r=$(($(od -An -N1 -i /dev/random))) \n
                \n
                # Generating a snapshot of the container to build the image from \n
                lxc snapshot ' . $container->getName() . '/"$r" \n
                \n
                # Build the image to be exported \n
                f=$(lxc publish ' . $container->getName() . '/"$r") \n
                fingerprint=${f##*: } \n
                \n
                # Make dir for backups in /tmp if it not exists \n
                if [ -d "$DIRECTORY" ]; then \n
                    rm -Rf "$DIRECTORY" \n
                fi \n
                mkdir "$DIRECTORY" \n
                \n
                # Export the image \n
                lxc image export "$fingerprint" "$DIRECTORY"' . $container->getName() . ' \n
                \n

                # Delete the snapshot \n
                lxc delete ' . $container->getName() . '/"$r" \n
                \n
                # Delete the image \n
                lxc image delete "$fingerprint"\n \n \n
            ';
        }

        $commandTexts = $commandTexts .
            '# Backup via duplicity \n
            duplicity ' . $this->type . ' --no-encryption /tmp/ ' . $this->name . ' ' . $this->destination->getDestinationText() . $this->name . ' \n \n
            # Make api call to webhook
            curl -X POST ' . $this->webhookUrl . ' \n
        \n\n';

        return $commandTexts;
    }


    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token) : void
    {
        $this->token = $token;
    }

    /**
     * Get webhook to generate a backup
     *
     * @return  string
     */
    public function getWebhookUrl()
    {
        return $this->webhookUrl;
    }

    /**
     * Set webhook to generate a backup
     *
     * @param  string  $webhookUrl  webhook to generate a backup
     *
     * @return  self
     */
    public function setWebhookUrl($webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;

        return $this;
    }
}