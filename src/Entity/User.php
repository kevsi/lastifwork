<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: "owner", targetEntity: Workspace::class)]
    private Collection $ownedWorkspaces;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Discussion::class)]
    private Collection $discussions;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Document::class)]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Multimedia::class)]
    private Collection $multimedias;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Notification::class)]
    private Collection $notifications;

    #[
        ORM\OneToMany(
            mappedBy: "user",
            targetEntity: WorkspaceMember::class,
            cascade: ["persist", "remove"]
        )
    ]
    private Collection $workspaceMemberships;

    public function __construct()
    {
        $this->ownedWorkspaces = new ArrayCollection();
        $this->discussions = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->multimedias = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->workspaceMemberships = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
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
        $roles[] = "ROLE_USER";

        return array_unique($roles);
    }

    /**
     * @param array $roles The roles to set
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function getOwnedWorkspaces(): Collection
    {
        return $this->ownedWorkspaces;
    }

    /**
     * @return Collection<int, WorkspaceMember>
     */
    public function getWorkspaceMemberships(): Collection
    {
        return $this->workspaceMemberships;
    }

    public function addWorkspaceMembership(WorkspaceMember $membership): static
    {
        if (!$this->workspaceMemberships->contains($membership)) {
            $this->workspaceMemberships->add($membership);
            $membership->setUser($this);
        }

        return $this;
    }

    public function removeWorkspaceMembership(
        WorkspaceMember $membership
    ): static {
        if ($this->workspaceMemberships->removeElement($membership)) {
            // set the owning side to null (unless already changed)
            if ($membership->getUser() === $this) {
                $membership->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Get all workspaces where this user is a member
     * Helper method to maintain compatibility with previous code
     *
     * @return Collection<int, Workspace>
     */
    public function getWorkspaces(): Collection
    {
        $workspaces = new ArrayCollection();
        foreach ($this->workspaceMemberships as $membership) {
            $workspaces->add($membership->getWorkspace());
        }
        return $workspaces;
    }
}
