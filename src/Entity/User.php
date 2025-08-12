<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.', errorPath: 'email', groups: ['create','edit'])]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà pris.',errorPath: 'pseudo', groups: ['create', 'edit'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email(message: 'Email non valide.', groups: ['edit'])]
    #[Assert\NotBlank(groups: ['edit'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'Un pseudo est necessaire', groups: ['edit'])]
    #[Assert\Length(
        min: 3,
        max: 180,
        minMessage: "Le pseudo doit faire au moins {{ limit }} caractères",
        maxMessage: "Le pseudo ne peut pas dépasser {{ limit }} caractères",
        groups: ['edit']
    )]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $pseudo = null;
    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank]
    #[ORM\Column]
    private ?string $password = null;



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?bool $eventAdmin = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 18, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Assert\NotNull(message: 'Veuillez sélectionner un campus', groups: ['edit'])]
    private ?Campus $campus = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur', orphanRemoval: true)]
    private Collection $sorties;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participants')]
    private Collection $sortiesInscrit;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFile = null;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->sortiesInscrit = new ArrayCollection();
        $this->event_admin = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isEventAdmin(): ?bool
    {
        return $this->eventAdmin;
    }

    public function setEventAdmin(bool $eventAdmin): static
    {
        $this->eventAdmin = $eventAdmin;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): static
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
            $sorty->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): static
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getOrganisateur() === $this) {
                $sorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesInscrit(): Collection
    {
        return $this->sortiesInscrit;
    }

    public function addSortiesInscrit(Sortie $sortiesInscrit): static
    {
        if (!$this->sortiesInscrit->contains($sortiesInscrit)) {
            $this->sortiesInscrit->add($sortiesInscrit);
            $sortiesInscrit->addParticipant($this);
        }

        return $this;
    }

    public function removeSortiesInscrit(Sortie $sortiesInscrit): static
    {
        if ($this->sortiesInscrit->removeElement($sortiesInscrit)) {
            $sortiesInscrit->removeParticipant($this);
        }

        return $this;
    }

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(?string $imageFile): static
    {
        $this->imageFile = $imageFile;

        return $this;
    }
}