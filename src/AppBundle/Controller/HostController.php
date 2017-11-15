<?php
/**
 * Created by PhpStorm.
 * User: Leon
 * Date: 06.11.2017
 * Time: 19:39
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Host;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;


class HostController extends Controller
{
    /**
     * @Route("/hosts", name="hosts_index", methods={"GET"})
     * @return Response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Zeigt eine Liste aller Hosts an",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Host::class, groups={"full"})
     *     )
     * )
     */
    public function indexAction()
    {
        $hosts = $this->getDoctrine()->getRepository(Host::class)->findAll();

        $serializer = $this->get('jms_serializer');
        $response = $serializer->serialize($hosts, 'json');
        return new Response($response);
    }


    /**
     * @Route("/hosts", name="hosts_store", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     *
     * @SWG\Response(
     *     response=201,
     *     description="speichert einen neuen host und gibt diesen zurück",
     *     @SWG\Schema(
     *         type="item",
     *         @Model(type=Host::class, groups={"full"})
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="ipv4",
     *     in="body",
     *     type="string",
     *     description="IPv4 Adresse des Hosts"
     * )
     * @SWG\Parameter(
     *     name="ipv6",
     *     in="body",
     *     type="string",
     *     description="IPv6 Adresse des Hosts"
     * )
     *@SWG\Parameter(
     *     name="domain_name",
     *     in="body",
     *     type="string",
     *     description="FQDN des Hosts"
     * )
     *@SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="Name des Hosts"
     * )
     * @SWG\Parameter(
     *     name="mac",
     *     in="body",
     *     type="string",
     *     description="MAC Adresse des Hosts"
     * )
     * @SWG\Parameter(
     *     name="settings",
     *     in="body",
     *     type="string",
     *     description="Sonstige Settings des Hosts"
     * )
     *
     * @SWG\Tag(name="hosts")
     */
    public function storeAction(Request $request, EntityManagerInterface $em)
    {

        $host = new Host();
        $host->setIpv4($request->request->get('ipv4'));
        $host->setIpv6($request->request->get('ipv6'));
        $host->setDomainName($request->request->get('domain_name'));
        $host->setMac($request->request->get('mac'));
        $host->setName($request->request->get('name'));
        $host->setSettings($request->request->get('settings'));
        $em->persist($host);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $response = $serializer->serialize($host, 'json');
        return new Response($response);
    }

    /**
     * @Route("/hosts/{id}", name="hosts_show", methods={"GET"})
     * @param int $id
     * @return Response
     *
     * @SWG\Parameter(
     *         description="ID von anzuzeigendem Host",
     *         format="int64",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="speichert einen neuen host und gibt diesen zurück",
     *     @SWG\Schema(
     *         type="item",
     *         @Model(type=Host::class, groups={"full"})
     *    )
     * )
     *
     * @SWG\Tag(name="hosts")
     */
    public function showAction($id)
    {
        $host = $this->getDoctrine()->getRepository(Host::class)->find($id);

        if (!$host) {
            throw $this->createNotFoundException(
                'No host found for id '.$id
            );
        }

        $serializer = $this->get('jms_serializer');
        $response = $serializer->serialize($host, 'json');
        return new Response($response);
    }

    /**
     * @Route("/hosts/{id}", name="hosts_update", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $em
     * @return Response
     * @SWG\Parameter(
     *     description="ID von upzudaten Host",
     *     format="int64",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="integer"
     * )
     * @SWG\Parameter(
     *     name="ipv4",
     *     in="body",
     *     type="string",
     *     description="IPv4 Adresse des Hosts"
     * )
     * @SWG\Parameter(
     *     name="ipv6",
     *     in="body",
     *     type="string",
     *     description="IPv6 Adresse des Hosts"
     * )
     * @SWG\Parameter(
     *     name="domain_name",
     *     in="body",
     *     type="string",
     *     description="FQDN des Hosts"
     * )
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="Name des Hosts"
     * )
     * @SWG\Parameter(
     *     name="mac",
     *     in="body",
     *     type="string",
     *     description="MAC Adresse des Hosts"
     * )
     * @SWG\Parameter(
     *     name="settings",
     *     in="body",
     *     type="string",
     *     description="Sonstige Settings des Hosts"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="speichert einen neuen host und gibt diesen zurück",
     *     @SWG\Schema(
     *         type="item",
     *         @Model(type=Host::class, groups={"full"})
     *    )
     * )
     *
     * @SWG\Tag(name="hosts")
     */
    public function updateAction(Request $request, $id, EntityManagerInterface $em)
    {
        $host = $this->getDoctrine()->getRepository(Host::class)->find($id);

        if (!$host) {
            throw $this->createNotFoundException(
                'No host found for id '.$id
            );
        }

        $host->setIpv4($request->request->get('ipv4'));
        $host->setIpv6($request->request->get('ipv6'));
        $host->setDomainName($request->request->get('domain_name'));
        $host->setMac($request->request->get('mac'));
        $host->setName($request->request->get('name'));
        $host->setSettings($request->request->get('settings'));
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $response = $serializer->serialize($host, 'json');
        return new Response($response);
    }

    /**
     * @Route("/hosts/{id}", name="hosts_delete", methods={"DELETE"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response
     *
     * @SWG\Parameter(
     *     description="ID des zu löschenden Host",
     *     format="int64",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="integer"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="löscht einen Host",
     * )
     */
    public function deleteAction($id, EntityManagerInterface $em)
    {
        $host = $this->getDoctrine()->getRepository(Host::class)->find($id);

        if (!$host) {
            throw $this->createNotFoundException(
                'No host found for id '.$id
            );
        }

        $em->remove($host);
        $em->flush();

        return $this->json(array('message' => 'erfolgreich gelöscht'));
    }

}