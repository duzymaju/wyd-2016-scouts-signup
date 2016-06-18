<?php

namespace Wyd2016Bundle\Controller\Admin;

use DateTime;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Repository\BaseRepositoryInterface;
use Wyd2016Bundle\Model\BandInterface;
use Wyd2016Bundle\Model\ParticipantAbstract;

/**
 * Abstract controller
 */
abstract class AbstractController extends Controller
{
    /**
     * Send reminder if requested
     *
     * @param ParticipantAbstract     $participant  participant
     * @param Request                 $request      request
     * @param BaseRepositoryInterface $repository   repository
     * @param string                  $confirmRoute confirm route
     * @param string                  $emailView    email view
     *
     * @return Response|null
     */
    protected function sendReminderIfRequested(ParticipantAbstract $participant, Request $request,
        BaseRepositoryInterface $repository, $confirmRoute, $emailView)
    {
        if (!$participant->isConfirmed()) {
            $sendReminder = (boolean) $request->query->get('sendReminder');
            if ($sendReminder && $participant->getUpdatedAt() < $this->getReminderDeadline()) {
                /** @var TranslatorInterface $translator */
                $translator = $this->get('translator');
                /** @var FlashBagInterface $sessionFlashBag */
                $sessionFlashBag = $this->get('session')
                    ->getFlashBag();

                if ($participant instanceof BandInterface) {
                    $leader = $participant->getLeader();
                    $email = $leader->getEmail();
                    $sex = $leader->getSex();
                    $locale = $this->getLocale($leader->getCountry());
                } else {
                    $email = $participant->getEmail();
                    $sex = $participant->getSex();
                    $locale = $this->getLocale($participant->getCountry());
                }

                $message = Swift_Message::newInstance()
                    ->setSubject($translator->trans('email.title', array(), null, $locale))
                    ->setFrom($this->getParameter('mailer_user'))
                    ->setTo($email)
                    ->setReplyTo($this->getParameter('wyd2016.email.reply_to'))
                    ->setBody($this->renderView($emailView, array(
                        'confirmationUrl' => $this->generateUrl($confirmRoute, array(
                                '_locale' => $locale,
                                'hash' => $participant->getActivationHash(),
                            ), UrlGeneratorInterface::ABSOLUTE_URL),
                        'locale' => $locale,
                        'sex' => $sex,
                    )), 'text/html');

                $mailer = $this->get('mailer');
                if ($mailer->send($message)) {
                    $participant->setUpdatedAt(new DateTime());
                    $repository->update($participant, true);
                    $sessionFlashBag->add('success', 'admin.reminder.success');
                } else {
                    $sessionFlashBag->add('error', 'admin.reminder.error');
                }

                return $this->redirect($this->generateUrl($request->get('_route'), array(
                    'id' => $participant->getId(),
                )));
            }
        }
    }

    /**
     * Is reminder sending possible
     * 
     * @param ParticipantAbstract $participant participant
     *
     * @return boolean
     */
    protected function isReminderSendingPossible(ParticipantAbstract $participant)
    {
        $isPossible = !$participant->isConfirmed() && $participant->getUpdatedAt() < $this->getReminderDeadline();

        return $isPossible;
    }

    /**
     * Get reminder deadline
     *
     * @return DateTime
     */
    protected function getReminderDeadline()
    {
        $reminderDeadline = (new DateTime())->modify('-1 month');

        return $reminderDeadline;
    }

    /**
     * Get locale
     *
     * @param string $country country
     *
     * @return string
     */
    protected function getLocale($country)
    {
        $locale = strtolower($country);
        if (!in_array($locale, $this->getParameter('locales'))) {
            $locale = $this->getParameter('locale');
        }

        return $locale;
    }

    /**
     * Get CSV from array
     * 
     * @param array $data data
     *
     * @return string
     */
    protected function getCsvFromArray(array $data)
    {
        $lines = array();
        foreach ($data as $row) {
            foreach ($row as $i => $cell) {
                if (strpos($cell, '"') !== false || strpos($cell, ' ') !== false || strpos($cell, ',') !== false) {
                    $row[$i] = '"' . str_replace('"', '""', $cell) . '"';
                }
            }
            $lines[] = implode(',', $row);
        }

        return implode("\r\n", $lines);
    }

    /**
     * Get CSV response
     *
     * @param array  $data     data
     * @param string $fileName file name
     *
     * @return Response
     */
    protected function getCsvResponse(array $data, $fileName = 'list')
    {
        $csv = $this->getCsvFromArray($data);

        return new Response($csv, Response::HTTP_OK, array(
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Content-Disposition' => 'attachment; filename=' . $fileName . '_' . date('Y-m-d') . '.csv',
            'Content-Type' => 'text/csv, charset=UTF-8',
            'Expires' => '0',
            'Pragma' => 'no-cache',
        ));
    }
}
