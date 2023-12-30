<?php

namespace Core\UserBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Product\Bundle\ProductBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlayCommand extends ContainerAwareCommand
{
    private $connection;

    private $parent = array();

    protected function configure()
    {
        $this->setName('app:play')
            ->setDescription('Archive all expired documents!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Playing.</info>');

     //   $this->play($output);
        $this->tree();

        $output->writeln('');

        $output->writeln('<info>Done.</info>');
    }

    protected function tree(){
        $em = $this->getEm();
        $repo = $em->getRepository('ProductProductBundle:Category');
        $options = array(
            'decorate' => true,
            'representationField' => 'slug',
            'html' => false
        );

        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            true, /* true: load all children, false: only direct */
            $options
        );

        print_r($htmlTree);
    }

    protected function play(OutputInterface $output)
    {
        $newCat = array();
        $em = $this->getEm();

        $oldCategories = $this->getOldCategories();

        foreach($this->parent as $categories) {
            foreach($categories as $id => $cat) {
                $newCat[$id] = $this->createNewCategory($oldCategories, $cat, $newCat, $em);
                $output->writeln('<info>Creating .</info>');
            }
        }

        $em->flush();
    }

    protected function getOldCategories()
    {
        $categories = $this->getConnection()->fetchAll("SELECT * FROM Category");

        $ret = array();

        foreach($categories as $category) {
            $ret[$category['id']] = $category;
            $this->parent[empty($category['parent'])?0:$category['parent']][$category['id']] = $category;
        }

        return $categories;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     * @throws \Exception
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        }

        if (!$this->connection->isConnected()) {
            $this->connection->connect();
        }

        return $this->connection;
    }


    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getEm()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param $oldCategories
     * @param $cat
     * @param $newCat
     * @param ObjectManager $em
     *
     * return Category
     */
    protected function createNewCategory($oldCategories, $cat, &$newCat, ObjectManager $em, $nest = 0)
    {
        if(isset($newCat[$cat['id']])) {
            return $newCat[$cat['id']];
        }

        $newCat[$cat['id']] = new Category();
        $newCat[$cat['id']]
            ->setId($cat['id'])
            ->setName($cat['name'])
            ->setStatus($cat['status'])
            ->setSorting($cat['sorting'])
            ->setFeature($cat['isFeature']);
        if (!empty($cat['parent'])) {

            if(!isset($newCat[$cat['parent']])) {
                echo "creating parent - nest" . $cat['parent'] . " - ". $nest . PHP_EOL;
                $nest++;
                $newCat[$cat['parent']] = $this->createNewCategory($oldCategories, $oldCategories[$cat['parent']], $newCat, $em, $nest);
            }

            $newCat[$cat['parent']] = $em->merge($newCat[$cat['parent']]);

            $newCat[$cat['id']]->setParent($newCat[$cat['parent']]);
        }

        echo "Processing : " . $cat['id'] . PHP_EOL;

        $em->persist($newCat[$cat['id']]);
        $em->flush($newCat[$cat['id']]);
        $em->clear();
        return $newCat[$cat['id']];
    }
}
