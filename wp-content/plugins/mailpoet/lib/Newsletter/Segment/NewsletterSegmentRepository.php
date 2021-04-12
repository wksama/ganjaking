<?php

namespace MailPoet\Newsletter\Segment;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterSegmentEntity;
use MailPoet\Newsletter\Options\NewsletterOptionsRepository;
use MailPoetVendor\Doctrine\ORM\EntityManager;

/**
 * @extends Repository<NewsletterSegmentEntity>
 */
class NewsletterSegmentRepository extends Repository {

  /** @var NewsletterOptionsRepository */
  private $newsletterOptionsRepository;

  public function __construct(
    NewsletterOptionsRepository $newsletterOptionsRepository,
    EntityManager $entityManager
  ) {
    parent::__construct($entityManager);
    $this->newsletterOptionsRepository = $newsletterOptionsRepository;
  }

  protected function getEntityClassName() {
    return NewsletterSegmentEntity::class;
  }

  public function getSubjectsOfActivelyUsedEmailsForSegments(array $segmentIds): array {
    $nameMap = [];
    // Welcome emails
    foreach ($this->newsletterOptionsRepository->findWelcomeNotificationsForSegments($segmentIds) as $option) {
      $newsletter = $option->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) continue;
      $nameMap[(string)$option->getValue()][] = $newsletter->getSubject();
    }
    // Automatic emails
    foreach ($this->newsletterOptionsRepository->findAutomaticEmailsForSegments($segmentIds) as $option) {
      $newsletter = $option->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) continue;
      $nameMap[(string)$option->getValue()][] = $newsletter->getSubject();
    }

    $otherNewsletters = $this->doctrineRepository->createQueryBuilder('ns')
      ->join('ns.newsletter', 'n')
      ->leftJoin('n.queues', 'q')
      ->leftJoin('q.task', 't')
      ->select('IDENTITY(ns.segment) AS segment_id, n.subject')
      ->where('(n.type = (:postNotification) OR n.status = :scheduled OR (t.id IS NOT NULL AND t.status IS NULL))')
      ->andWhere('ns.segment IN (:segmentIds)')
      ->setParameter('postNotification', NewsletterEntity::TYPE_NOTIFICATION)
      ->setParameter('segmentIds', $segmentIds)
      ->setParameter('scheduled', NewsletterEntity::STATUS_SCHEDULED)
      ->addGroupBy('ns.segment, q.id, t.id')
      ->getQuery()
      ->getResult();

    foreach ($otherNewsletters as $result) {
      if (isset($nameMap[(string)$result['segment_id']]) && in_array($result['subject'], $nameMap[(string)$result['segment_id']])) {
        continue;
      }
      $nameMap[(string)$result['segment_id']][] = $result['subject'];
    }
    return $nameMap;
  }

  public function getSendingEmailSubjectsBySegmentIds(array $segmentIds): array {
    $results = $this->doctrineRepository->createQueryBuilder('ns')
      ->select('IDENTITY(ns.segment) AS segment_id, n.subject')
      ->join('ns.newsletter', 'n')
      ->join('n.queues', 'q')
      ->join('q.task', 't')
      ->where('t.status IS NULL')
      ->andWhere('ns.segment IN (:segmentIds)')
      ->setParameter('segmentIds', $segmentIds)
      ->getQuery()
      ->getResult();

    $nameMap = [];
    foreach ($results as $result) {
      $nameMap[(string)$result['segment_id']][] = $result['subject'];
    }
    return $nameMap;
  }
}
