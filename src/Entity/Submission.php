<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Submission {
    use IdTrait;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $date = null;

    #[ORM\Column(type: 'string')]
    private ?string $form = null;

    /**
     * @var mixed
     */
    #[ORM\Column(type: 'json')]
    #[Assert\NotNull]
    private $data;

    /**
     * @return string
     */
    public function getForm(): string {
        return $this->form;
    }

    /**
     * @return Submission
     */
    public function setForm(string $form): Submission {
        $this->form = $form;
        return $this;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data): Submission {
        $this->data = $data;
        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateDate(): void {
        $this->date = new DateTime();
    }

    public function getDate(): DateTime {
        return $this->date;
    }
}