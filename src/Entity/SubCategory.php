<?php

namespace App\Entity;

use App\Repository\SubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubCategoryRepository::class)]
class SubCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'subCategories')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'subcategory', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'subcategory', targetEntity: RecurringTransaction::class)]
    private Collection $recurringTransactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->recurringTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setSubcategory($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSubcategory() === $this) {
                $transaction->setSubcategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RecurringTransaction>
     */
    public function getRecurringTransactions(): Collection
    {
        return $this->recurringTransactions;
    }

    public function addRecurringTransaction(RecurringTransaction $recurringTransaction): self
    {
        if (!$this->recurringTransactions->contains($recurringTransaction)) {
            $this->recurringTransactions->add($recurringTransaction);
            $recurringTransaction->setSubcategory($this);
        }

        return $this;
    }

    public function removeRecurringTransaction(RecurringTransaction $recurringTransaction): self
    {
        if ($this->recurringTransactions->removeElement($recurringTransaction)) {
            // set the owning side to null (unless already changed)
            if ($recurringTransaction->getSubcategory() === $this) {
                $recurringTransaction->setSubcategory(null);
            }
        }

        return $this;
    }
}
