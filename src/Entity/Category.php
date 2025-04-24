<?php
// src/Entity/Category.php
namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\CategoryStatus;
use Symfony\Component\String\Slugger\AsciiSlugger;


#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
#[ORM\HasLifecycleCallbacks]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type:"string", length:10, unique:true)]
    private $code;

    #[ORM\Column(type: 'string', enumType: CategoryStatus::class)]
    private ?CategoryStatus $status = null;
    
    
    #[ORM\Column(type:"string", length:20, nullable:true)]
    private $color;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?self $parent = null;

    /**
    * @var Collection|Category[]
    */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private $children;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Discussion::class)]
    private Collection $discussions;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Document::class)]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Multimedia::class)]
    private Collection $multimedias;

    public function __construct()
    {
        $this->discussions = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->multimedias = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->children = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    


    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
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

    public function addDiscussion(Discussion $discussion): static
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setCategory($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): static
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getCategory() === $this) {
                $discussion->setCategory(null);
            }
        }

        return $this;
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

    public function getStatus(): ?CategoryStatus
    {
        return $this->status;
    }
    
    public function setStatus(CategoryStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getCode(): ?string
    {   
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }


    public function addChild(Category $child): self
    {
        if (!$this->children->contains($child)) {
        $this->children[] = $child;
        $child->setParent($this);
    }
        return $this;
    }

    public function removeChild(Category $child): self
    {
        if ($this->children->removeElement($child)) {
        // set the owning side to null
            if ($child->getParent() === $this) {
            $child->setParent(null);
        }
    }
        return $this;
    }


    /**
 * @ORM\PrePersist
 * @ORM\PreUpdate
 */
        #[ORM\PrePersist]
        #[ORM\PreUpdate]
        public function generateCode()
        {
            if (empty($this->code)) {
            // Prendre les 3 premiers caractères du nom en majuscules
            $baseCode = substr(strtoupper(trim($this->name)), 0, 3);
                
            // Enlever les caractères spéciaux/accents
            $slugger = new AsciiSlugger();
            $baseCode = $slugger->slug($baseCode, '')->toString();
                
            // Si c'est une sous-catégorie, préfixer avec le code parent
            if ($this->parent) {
                $this->code = $this->parent->getCode() . '-' . $baseCode;
            } else {
                $this->code = $baseCode;
            }
        }
        }
}