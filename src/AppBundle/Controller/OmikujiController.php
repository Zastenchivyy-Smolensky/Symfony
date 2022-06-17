<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;   // (a)
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // (b)
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Unsei;                // (a)
use AppBundle\Repository\UnseiRepository;
use Doctrine\ORM\EntityManager;


class OmikujiController extends Controller  // ①
{

    /**
     * おみくじ運勢を表示する
     * @Route("/omikuji/{yourname}", defaults={"yourname" = "YOU"}, name="omikuji")
     * 
     * @param Request $request
     * @return Response
     */
    public function omikujiAction(Request $request, $yourname)  // ③
    {
        $repository = $this->getDoctrine()->getRepository(Unsei::class); // ①
        $omikuji = $repository->findAll(); // ②
        
        // $omikuji = ['大吉', '中吉', '小吉', '末吉', '凶'];
        $number = rand(0, count($omikuji) - 1);

        return $this->render('omikuji/omikuji.html.twig', [
            'name' => $yourname,
            'unsei' => $omikuji[$number],
        ]);
    }
    /**
     * @Route("/find")
     */
    public function findAction()
    {
        /**
         * @var UnseiRepository $repository
         */
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $unseis = $repository->findAll();
        dump($unseis);
        $unsei = $repository->find(1);
        dump($unsei);
        $unsei = $repository->findOneBy([
            'name' => '大吉',
        ]);
        dump($unsei);
        $unsei = $repository->findBy([
            'name' => '大吉',
        ]);
        dump($unsei);

        $unsei = $repository->findOneById(1);
        dump($unsei);
        $unsei = $repository->findOneByName('中吉');
        dump($unsei);
        $unsei =$repository->findByName("中吉");
        dump($unsei);

        die;
        return new Response("Dummy");
    }
    /**
     * @Route("/crud")
     */
    public function crudAction()
    {
        /**
         * エンティティの作成、更新、削除はエンティティマネージャを通して行う ①
         *  @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        //
        // Create
        //
        $unsei = new Unsei();    // ②
        $unsei->setName("大凶");
        dump($unsei);

        $em->persist($unsei);    // ③
        $em->flush();            // ④
        dump($unsei);

        //
        // Read ⑤
        //
        $repository = $em->getRepository(Unsei::class);
        
        /** @var Unsei $unsei */
        $unsei = $repository->findOneByName('大凶'); // ⑥
        dump($unsei);
        
        //
        // Update ⑦
        //
        $unsei->setName("大大吉");
        $em->flush();
        dump($unsei);
        
        $unsei = $repository->find($unsei->getId());
        dump($unsei);
        
        // 
        // Delete ⑧
        // 
        $em->remove($unsei);
        $em->flush();
        
        $unseis = $repository->findAll();
        dump($unseis);
        foreach ($unseis as $unsei) {
            dump($unsei->getName());
        }
        
        die; // プログラムを終了して、dumpを画面に表示

        return new Response("Dummy");
    }    
    /**
     * @Route("/dql")
     */
    public function dql()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT u
            FROM AppBundle:Unsei u
            where u.name = :name"
        )->setParameter("name", "大吉");
        $unsei = $query->getResult();
        dump($unsei);
        die;
        return new Response("Dummy");
    }
    /**
     * @Route("/qd")
     */
    public function queryBuilder()
    {
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $query = $repository->creaeteQueryBuilder("u")
            ->where("u.name=:name")
            ->setParamter("name","大吉")
            ->getQuery();
        $unsei= $query->getResult();
        dump($unsei);
        die;
        return new Response("Dummy");
    }
}