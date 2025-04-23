<?php
// src/Entity/Workspace.php
namespace App\Entity;

use App\Repository\WorkspaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
#[ORM\Table(name: 'workspaces')]
#[ORM\HasLifecycleCallbacks]
class Workspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'ownedWorkspaces')]
    #[ORM\JoinColumn(name: 'owner_id', nullable: false)]
    private ?User $owner = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: Discussion::class)]
    private Collection $discussions;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: Document::class)]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: Multimedia::class)]
    private Collection $multimedias;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: WorkspaceMember::class, cascade: ['persist', 'remove'])]
    private Collection $workspaceMembers;

    public function __construct()
    {
        $this->discussions = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->multimedias = new ArrayCollection();
        $this->workspaceMembers = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
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
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @return Collection<int, Multimedia>
     */
    public function getMultimedias(): Collection
    {
        return $this->multimedias;
    }

    /**
     * @return Collection<int, WorkspaceMember>
     */
    public function getWorkspaceMembers(): Collection
    {
        return $this->workspaceMembers;
    }

    public function addWorkspaceMember(WorkspaceMember $member): static
    {
        if (!$this->workspaceMembers->contains($member)) {
            $this->workspaceMembers->add($member);
            $member->setWorkspace($this);
        }

        return $this;
    }

    public function removeWorkspaceMember(WorkspaceMember $member): static
    {
        if ($this->workspaceMembers->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getWorkspace() === $this) {
                $member->setWorkspace(null);
            }
        }
        
        return $this;
    }

    /**
     * Get all users that are members of this workspace
     * Helper method to maintain compatibility with previous code
     * 
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        $members = new ArrayCollection();
        foreach ($this->workspaceMembers as $membership) {
            $members->add($membership->getUser());
        }
        return $members;
    }

    public function addMember(User $user, string $role = 'member'): static
    {
        $member = new WorkspaceMember();
        $member->setUser($user);
        $member->setWorkspace($this);
        $member->setRole($role);
        
        $this->addWorkspaceMember($member);
        
        return $this;
    }

    public function removeMember(User $user): static
    {
        foreach ($this->workspaceMembers as $member) {
            if ($member->getUser() === $user) {
                $this->workspaceMembers->removeElement($member);
            }
        }
        
        return $this;
    }
}