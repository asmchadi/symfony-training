<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @Assert\NotNull(message="Please provide you name")
     * @Assert\NotBlank(message="Let us know you name")
     * @var string
     */
    private $name;

    /**
     * @Assert\Email(message="Please provide a valid email address")
     * @Assert\NotBlank(message="Your email est mandatory")
     * @var string
     */
    private $email;

    /**
     * @Assert\NotNull(message="The subject is mandatory")
     * @Assert\NotBlank(message="Tell us what your subject")
     * @var string
     */
    private $subject;

    /**
     * @Assert\NotBlank(message="Please tell us more about your request")
     * @var string
     */
    private $message;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
