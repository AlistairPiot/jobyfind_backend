<?php

namespace App\Tests\Entity;

use App\Entity\Skill;
use App\Entity\SkillCategory;
use PHPUnit\Framework\TestCase;

class SkillTest extends TestCase
{
    public function testSetName()
    {
        // Création de l'entité Skill
        $skill = new Skill();

        // Définir un nom pour la compétence
        $skill->setName('PHP');

        // Vérification que le nom est bien défini
        $this->assertEquals('PHP', $skill->getName());
    }

    public function testAddSkillCategory()
    {
        // Création des entités Skill et SkillCategory
        $skill = new Skill();
        $category = new SkillCategory();

        // Ajouter la catégorie à la compétence
        $skill->addSkillCategory($category);

        // Vérification que la catégorie a bien été ajoutée
        $this->assertCount(1, $skill->getSkillCategory());
        $this->assertTrue($skill->getSkillCategory()->contains($category));
    }
}

