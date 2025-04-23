<?php
// src/Entity/WorkspaceMember.php
namespace App\Entity;

use App\Repository\WorkspaceMembersRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity(repositoryClass: WorkspaceMembersRepository::class)]
#[ORM\Table(name: 'workspace_members')]
class WorkspaceMember
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'workspaceMembers')]
    #[ORM\JoinColumn(name: 'workspace_id', referencedColumnName: 'id', nullable: false)]
    private ?Workspace $workspace = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'workspaceMemberships')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 50, options: ['default' => 'member'])]
    private string $role = 'member';

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $joinedAt;

    public function __construct()
    {
        $this->joinedAt = new DateTime();
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): self
    {
        $this->workspace = $workspace;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getJoinedAt(): DateTime
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(DateTime $joinedAt): self
    {
        $this->joinedAt = $joinedAt;
        return $this;
    }
}