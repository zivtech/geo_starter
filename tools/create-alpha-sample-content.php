<?php

declare(strict_types=1);

use Drupal\node\Entity\Node;

$termUuids = [
  'audience' => [
    'residents' => '3dcd92ee-13c9-4da0-a525-92fbfc4216a5',
    'families' => '4f04ae7a-4b70-4800-83ae-947daeb6b934',
    'small_businesses' => '1f67ce47-1fbb-4892-8c4c-5b6b0f122404',
    'community_partners' => 'fcc1dc48-4eb7-4718-9a1c-07a73a6f9156',
    'professionals' => '7763e035-f28f-44a7-b5ae-289c26a78e3e',
  ],
  'topic' => [
    'eligibility' => '15354b34-fe0b-4470-8f1f-8b5f1dddfa0f',
    'application_steps' => 'aff6ee5a-d419-4940-92b8-3d9c8810f229',
    'required_documents' => 'c9395a5d-3462-4db9-bfb4-70042f1b4a36',
    'deadlines' => '5ed66004-3b85-461b-bbdc-0637a10c9c8c',
    'costs' => 'a4a3e9ce-b781-4ee1-92a9-4ccba3217bc8',
    'appeals' => 'a9e3da47-2d20-45c4-b312-f600adbabbb1',
    'accessibility' => '546e56f1-4b32-4304-9019-aff6daad5c28',
    'contact' => '8463ae30-23c1-41d3-a031-3a57fa7cb537',
  ],
  'service_area' => [
    'benefits' => '051a0930-6e66-446b-a28f-fef4d5640bdc',
    'permits' => '2dba0288-b9b6-41f7-b12f-3d6750756a87',
    'community_programs' => '63c603f6-b3bd-4d7f-a1bd-453174d1013b',
    'housing_utilities' => '782e744d-5d69-4c94-b5d1-bf62e431c9e2',
  ],
];

$evidenceSources = [
  'demo_benefits_guide' => [
    'uuid' => '40000000-0000-4000-8000-000000000001',
    'title' => 'Demo City Benefits Guide',
    'publisher' => 'Demo City Department of Resident Services',
    'url' => 'https://example.org/demo-city/benefits-guide',
    'url_title' => 'Benefits guide',
    'source_type' => 'guideline',
    'date' => '2026-01-10',
    'note' => 'Fictional public-service guidance used to demonstrate visible source attribution.',
  ],
  'permit_records_checklist' => [
    'uuid' => '40000000-0000-4000-8000-000000000002',
    'title' => 'Permit Records Request Checklist',
    'publisher' => 'Demo City Records Office',
    'url' => 'https://example.org/demo-city/permit-records-checklist',
    'url_title' => 'Permit records checklist',
    'source_type' => 'program_page',
    'date' => '2026-02-05',
    'note' => 'Fictional checklist showing the evidence pattern for procedural service pages.',
  ],
  'utility_assistance_policy' => [
    'uuid' => '40000000-0000-4000-8000-000000000003',
    'title' => 'Utility Assistance Review Policy',
    'publisher' => 'Demo City Housing and Utilities Office',
    'url' => 'https://example.org/demo-city/utility-assistance-policy',
    'url_title' => 'Utility assistance policy',
    'source_type' => 'policy',
    'date' => '2026-03-12',
    'note' => 'Fictional policy source used to demonstrate last-reviewed and appeal language.',
  ],
  'community_program_calendar' => [
    'uuid' => '40000000-0000-4000-8000-000000000004',
    'title' => 'Neighborhood Skills Program Calendar',
    'publisher' => 'Demo City Community Programs Office',
    'url' => 'https://example.org/demo-city/neighborhood-skills-calendar',
    'url_title' => 'Program calendar',
    'source_type' => 'program_page',
    'date' => '2026-04-01',
    'note' => 'Fictional program listing for sample community-program content.',
  ],
  'accessibility_services_guide' => [
    'uuid' => '40000000-0000-4000-8000-000000000005',
    'title' => 'Accessible Services Guide',
    'publisher' => 'Demo City Office of Access and Language Support',
    'url' => 'https://example.org/demo-city/accessible-services-guide',
    'url_title' => 'Accessible services guide',
    'source_type' => 'guideline',
    'date' => '2026-04-16',
    'note' => 'Fictional source for demonstrating accommodation and language-support references.',
  ],
  'appeal_process_faq' => [
    'uuid' => '40000000-0000-4000-8000-000000000006',
    'title' => 'Appeal Process FAQ',
    'publisher' => 'Demo City Service Review Office',
    'url' => 'https://example.org/demo-city/appeal-process-faq',
    'url_title' => 'Appeal process FAQ',
    'source_type' => 'faq',
    'date' => '2026-02-22',
    'note' => 'Fictional FAQ source; the starter does not claim broad FAQ rich-result eligibility.',
  ],
];

$services = [
  'emergency_assistance' => [
    'uuid' => '41000000-0000-4000-8000-000000000001',
    'title' => 'Apply for emergency food and utility assistance',
    'direct_answer' => 'Residents can apply for short-term food and utility assistance when a household expense or income change creates an urgent need.',
    'summary' => 'A source-backed service page that explains who the demo assistance program is for, what to prepare, and where to start.',
    'problem' => 'A resident needs a clear first step when an urgent household cost makes food or utility payments hard to cover.',
    'audience' => ['residents', 'families'],
    'service_area' => ['benefits'],
    'topic' => ['eligibility', 'application_steps', 'required_documents'],
    'eligibility' => 'This fictional demo service is written for residents who live in Demo City and can show a recent urgent need.',
    'next_action_uri' => 'https://example.org/demo-city/apply-emergency-assistance',
    'next_action_title' => 'Start the demo assistance application',
    'evidence' => ['demo_benefits_guide', 'accessibility_services_guide'],
    'reviewed_date' => '2026-05-10',
  ],
  'permit_records' => [
    'uuid' => '41000000-0000-4000-8000-000000000002',
    'title' => 'Request a home repair permit record',
    'direct_answer' => 'Property owners and authorized representatives can request a copy of a Demo City home repair permit record online or by appointment.',
    'summary' => 'A procedural service page showing required details, expected costs, and how records staff review requests.',
    'problem' => 'A resident or contractor needs a permit record before repair, sale, insurance, or inspection work can continue.',
    'audience' => ['residents', 'small_businesses', 'professionals'],
    'service_area' => ['permits'],
    'topic' => ['required_documents', 'costs', 'contact'],
    'eligibility' => 'This fictional demo service is for property owners, tenants with authorization, licensed contractors, and public-record requesters.',
    'next_action_uri' => 'https://example.org/demo-city/request-permit-record',
    'next_action_title' => 'Request a demo permit record',
    'evidence' => ['permit_records_checklist'],
    'reviewed_date' => '2026-05-08',
  ],
  'skills_program' => [
    'uuid' => '41000000-0000-4000-8000-000000000003',
    'title' => 'Join a neighborhood skills program',
    'direct_answer' => 'Adults and older teens can register for free neighborhood skills sessions offered through fictional community partners.',
    'summary' => 'A community-program service page that demonstrates audience labels, program dates, and partner-facing language.',
    'problem' => 'A resident wants practical classes and support programs but needs one reliable page that explains what is available.',
    'audience' => ['residents', 'community_partners'],
    'service_area' => ['community_programs'],
    'topic' => ['application_steps', 'deadlines', 'accessibility'],
    'eligibility' => 'This fictional demo service is open to residents age 16 and older; some partner sessions may ask for advance registration.',
    'next_action_uri' => 'https://example.org/demo-city/neighborhood-skills-program',
    'next_action_title' => 'View demo program sessions',
    'evidence' => ['community_program_calendar', 'accessibility_services_guide'],
    'reviewed_date' => '2026-05-06',
  ],
  'water_bill_help' => [
    'uuid' => '41000000-0000-4000-8000-000000000004',
    'title' => 'Get help with a past-due water bill',
    'direct_answer' => 'Households with a past-due Demo City water bill can ask for a payment plan, hardship review, or referral before service is interrupted.',
    'summary' => 'A utilities service page showing how structured content can connect deadlines, appeals, and source citations.',
    'problem' => 'A household needs to understand options before a missed payment becomes a service interruption.',
    'audience' => ['residents', 'families'],
    'service_area' => ['housing_utilities'],
    'topic' => ['deadlines', 'appeals', 'contact'],
    'eligibility' => 'This fictional demo service is for account holders or authorized household members with a past-due balance.',
    'next_action_uri' => 'https://example.org/demo-city/water-bill-help',
    'next_action_title' => 'Request demo water bill help',
    'evidence' => ['utility_assistance_policy', 'appeal_process_faq'],
    'reviewed_date' => '2026-05-12',
  ],
];

$answers = [
  'assistance_eligibility' => [
    'uuid' => '42000000-0000-4000-8000-000000000001',
    'title' => 'Who can apply for emergency assistance?',
    'direct_answer' => 'In the fictional demo program, residents apply when they live in Demo City and have a recent urgent food, housing, or utility need.',
    'body' => 'The sample answer keeps eligibility criteria visible, sourceable, and short enough to reuse on related pages.',
    'topic' => ['eligibility'],
    'audience' => ['residents', 'families'],
    'services' => ['emergency_assistance', 'water_bill_help'],
    'evidence' => ['demo_benefits_guide', 'utility_assistance_policy'],
    'reviewed_date' => '2026-05-11',
  ],
  'documents_needed' => [
    'uuid' => '42000000-0000-4000-8000-000000000002',
    'title' => 'What documents should I prepare before I apply?',
    'direct_answer' => 'Prepare proof of identity, a Demo City address, the bill or notice involved, and any document that explains the urgent need.',
    'body' => 'This fictional checklist illustrates how answer pages can cite a program guide without hiding the source inside metadata only.',
    'topic' => ['required_documents'],
    'audience' => ['residents', 'families'],
    'services' => ['emergency_assistance', 'water_bill_help'],
    'evidence' => ['demo_benefits_guide', 'permit_records_checklist'],
    'reviewed_date' => '2026-05-11',
  ],
  'review_timeline' => [
    'uuid' => '42000000-0000-4000-8000-000000000003',
    'title' => 'How long does a review usually take?',
    'direct_answer' => 'Most fictional demo requests list an expected review window on the service page; urgent assistance examples use a short review window.',
    'body' => 'The starter avoids promising real processing times. Demo timelines are here to show how date-sensitive claims should stay reviewable.',
    'topic' => ['deadlines'],
    'audience' => ['residents'],
    'services' => ['emergency_assistance', 'permit_records', 'water_bill_help'],
    'evidence' => ['demo_benefits_guide', 'permit_records_checklist'],
    'reviewed_date' => '2026-05-09',
  ],
  'appeal_denial' => [
    'uuid' => '42000000-0000-4000-8000-000000000004',
    'title' => 'Can I appeal if my application is denied?',
    'direct_answer' => 'Yes. The fictional demo service includes an appeal step so editors can model how denial language, deadlines, and sources stay visible.',
    'body' => 'This answer is intentionally general. A real service would need jurisdiction-specific appeal rules and legal review.',
    'topic' => ['appeals'],
    'audience' => ['residents'],
    'services' => ['emergency_assistance', 'water_bill_help'],
    'evidence' => ['appeal_process_faq', 'utility_assistance_policy'],
    'reviewed_date' => '2026-05-12',
  ],
  'permit_record_request' => [
    'uuid' => '42000000-0000-4000-8000-000000000005',
    'title' => 'How do I request permit records?',
    'direct_answer' => 'Use the fictional request form, include the property address, and choose whether you need a digital copy or an appointment.',
    'body' => 'The answer demonstrates procedural reuse between a service page and a records-focused question page.',
    'topic' => ['application_steps', 'required_documents'],
    'audience' => ['residents', 'small_businesses', 'professionals'],
    'services' => ['permit_records'],
    'evidence' => ['permit_records_checklist'],
    'reviewed_date' => '2026-05-08',
  ],
  'expected_costs' => [
    'uuid' => '42000000-0000-4000-8000-000000000006',
    'title' => 'What costs should I expect?',
    'direct_answer' => 'The fictional demo content separates free services from possible copy, replacement, or program fees so users can scan costs quickly.',
    'body' => 'A real site should connect each cost statement to its current source and review date.',
    'topic' => ['costs'],
    'audience' => ['residents', 'small_businesses'],
    'services' => ['permit_records', 'skills_program'],
    'evidence' => ['permit_records_checklist', 'community_program_calendar'],
    'reviewed_date' => '2026-05-07',
  ],
  'accessibility_help' => [
    'uuid' => '42000000-0000-4000-8000-000000000007',
    'title' => 'Is language or accessibility help available?',
    'direct_answer' => 'Yes. The fictional demo services include a visible accommodation path and language-support contact before users start a request.',
    'body' => 'The sample answer reinforces that accessibility information should be visible on the page, not only encoded as metadata.',
    'topic' => ['accessibility', 'contact'],
    'audience' => ['residents', 'families', 'community_partners'],
    'services' => ['emergency_assistance', 'skills_program', 'water_bill_help'],
    'evidence' => ['accessibility_services_guide'],
    'reviewed_date' => '2026-05-10',
  ],
  'contact_before_visit' => [
    'uuid' => '42000000-0000-4000-8000-000000000008',
    'title' => 'Who should I contact before visiting an office?',
    'direct_answer' => 'Contact the fictional service office listed on the service page, especially when documents, accessibility support, or appointments are involved.',
    'body' => 'This answer is a compact contact pattern for services that may need in-person support.',
    'topic' => ['contact'],
    'audience' => ['residents', 'professionals'],
    'services' => ['permit_records', 'water_bill_help'],
    'evidence' => ['accessibility_services_guide', 'permit_records_checklist'],
    'reviewed_date' => '2026-05-09',
  ],
];

$articles = [
  'reviewable_pages' => [
    'uuid' => '43000000-0000-4000-8000-000000000001',
    'title' => 'How the answer hub keeps public-service pages reviewable',
    'summary' => 'A sample article showing why sources, review dates, and structured relationships matter for public-service content.',
    'body' => 'Public-service pages age quickly when eligibility, documents, contacts, or deadlines change. This fictional article demonstrates how the starter keeps claims close to visible sources and review dates.',
    'topic' => ['accessibility', 'contact'],
    'audience' => ['community_partners', 'professionals'],
    'author' => 'GEO Starter Demo Team',
    'services' => ['emergency_assistance', 'permit_records'],
    'answers' => ['documents_needed', 'accessibility_help'],
    'evidence' => ['demo_benefits_guide', 'accessibility_services_guide'],
    'reviewed_date' => '2026-05-13',
  ],
  'complete_application' => [
    'uuid' => '43000000-0000-4000-8000-000000000002',
    'title' => 'Preparing a complete assistance application',
    'summary' => 'A demo explainer that connects application steps, required documents, and follow-up questions.',
    'body' => 'The article models a plain-language explainer that links out to service pages and reusable answers instead of duplicating every instruction.',
    'topic' => ['application_steps', 'required_documents'],
    'audience' => ['residents', 'families'],
    'author' => 'GEO Starter Demo Team',
    'services' => ['emergency_assistance', 'water_bill_help'],
    'answers' => ['assistance_eligibility', 'documents_needed', 'review_timeline'],
    'evidence' => ['demo_benefits_guide', 'utility_assistance_policy'],
    'reviewed_date' => '2026-05-14',
  ],
  'multiple_sources' => [
    'uuid' => '43000000-0000-4000-8000-000000000003',
    'title' => 'What to do when a service page cites more than one source',
    'summary' => 'A sample governance article for editors who need to keep source-backed claims understandable.',
    'body' => 'When a page cites more than one source, editors should make the claim, source, and last review date visible. This fictional article shows how related answers and evidence sources can support that workflow.',
    'topic' => ['eligibility', 'appeals'],
    'audience' => ['professionals', 'community_partners'],
    'author' => 'GEO Starter Demo Team',
    'services' => ['water_bill_help', 'skills_program'],
    'answers' => ['appeal_denial', 'expected_costs'],
    'evidence' => ['appeal_process_faq', 'community_program_calendar'],
    'reviewed_date' => '2026-05-15',
  ],
];

$created = [];

deleteExistingNodes(['service', 'answer', 'article', 'evidence_source']);

foreach ($evidenceSources as $key => $source) {
  $created['evidence'][$key] = createEvidenceSource($source);
}
foreach ($services as $key => $service) {
  $created['services'][$key] = createService($service, $termUuids, $created['evidence']);
}
foreach ($answers as $key => $answer) {
  $created['answers'][$key] = createAnswer($answer, $termUuids, $created['services'], $created['evidence']);
}
foreach ($articles as $key => $article) {
  $created['articles'][$key] = createArticle($article, $termUuids, $created['services'], $created['answers'], $created['evidence']);
}

echo "Created sample content:\n";
foreach ($created as $group => $items) {
  echo sprintf("- %s: %d\n", $group, count($items));
}

function deleteExistingNodes(array $bundles): void {
  $storage = \Drupal::entityTypeManager()->getStorage('node');
  $ids = \Drupal::entityQuery('node')
    ->accessCheck(FALSE)
    ->condition('type', $bundles, 'IN')
    ->execute();
  if ($ids) {
    $storage->delete($storage->loadMultiple($ids));
  }
}

function createEvidenceSource(array $source): Node {
  $node = Node::create([
    'uuid' => $source['uuid'],
    'type' => 'evidence_source',
    'title' => $source['title'],
    'field_publisher' => $source['publisher'],
    'field_source_url' => [
      'uri' => $source['url'],
      'title' => $source['url_title'],
    ],
    'field_source_type' => $source['source_type'],
    'field_publication_date' => $source['date'],
    'field_credibility_note' => textValue($source['note']),
    'status' => TRUE,
    'moderation_state' => 'published',
    'path' => ['alias' => '/sources/' . slug($source['title'])],
  ]);
  $node->save();
  return $node;
}

function createService(array $service, array $termUuids, array $evidence): Node {
  $node = Node::create([
    'uuid' => $service['uuid'],
    'type' => 'service',
    'title' => $service['title'],
    'field_direct_answer' => textValue($service['direct_answer']),
    'field_summary' => textValue($service['summary']),
    'field_problem_solved' => textValue($service['problem']),
    'field_audience' => termRefs($service['audience'], $termUuids['audience']),
    'field_service_area' => termRefs($service['service_area'], $termUuids['service_area']),
    'field_topic' => termRefs($service['topic'], $termUuids['topic']),
    'field_eligibility' => textValue($service['eligibility']),
    'field_next_action' => [
      'uri' => $service['next_action_uri'],
      'title' => $service['next_action_title'],
    ],
    'field_evidence_sources' => nodeRefs($service['evidence'], $evidence),
    'field_reviewed_by_name' => 'Demo Services Editorial Review',
    'field_reviewed_date' => $service['reviewed_date'],
    'status' => TRUE,
    'moderation_state' => 'published',
    'path' => ['alias' => '/services/' . slug($service['title'])],
  ]);
  $node->save();
  return $node;
}

function createAnswer(array $answer, array $termUuids, array $services, array $evidence): Node {
  $node = Node::create([
    'uuid' => $answer['uuid'],
    'type' => 'answer',
    'title' => $answer['title'],
    'field_direct_answer' => textValue($answer['direct_answer']),
    'body' => textValue($answer['body']),
    'field_topic' => termRefs($answer['topic'], $termUuids['topic']),
    'field_audience' => termRefs($answer['audience'], $termUuids['audience']),
    'field_related_services' => nodeRefs($answer['services'], $services),
    'field_evidence_sources' => nodeRefs($answer['evidence'], $evidence),
    'field_reviewed_by_name' => 'Demo Services Editorial Review',
    'field_reviewed_date' => $answer['reviewed_date'],
    'status' => TRUE,
    'moderation_state' => 'published',
    'path' => ['alias' => '/answers/' . slug($answer['title'])],
  ]);
  $node->save();
  return $node;
}

function createArticle(array $article, array $termUuids, array $services, array $answers, array $evidence): Node {
  $node = Node::create([
    'uuid' => $article['uuid'],
    'type' => 'article',
    'title' => $article['title'],
    'field_summary' => textValue($article['summary']),
    'body' => textValue($article['body']),
    'field_topic' => termRefs($article['topic'], $termUuids['topic']),
    'field_audience' => termRefs($article['audience'], $termUuids['audience']),
    'field_author_name' => $article['author'],
    'field_reviewed_by_name' => 'Demo Services Editorial Review',
    'field_evidence_sources' => nodeRefs($article['evidence'], $evidence),
    'field_related_services' => nodeRefs($article['services'], $services),
    'field_related_answers' => nodeRefs($article['answers'], $answers),
    'field_reviewed_date' => $article['reviewed_date'],
    'status' => TRUE,
    'moderation_state' => 'published',
    'path' => ['alias' => '/articles/' . slug($article['title'])],
  ]);
  $node->save();
  return $node;
}

function textValue(string $value): array {
  return [
    'value' => $value,
    'format' => 'content_format',
  ];
}

function termRefs(array $keys, array $uuidMap): array {
  $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
  return array_map(static function (string $key) use ($uuidMap, $storage): array {
    $term = $storage->loadByProperties(['uuid' => $uuidMap[$key]]);
    return ['target_id' => reset($term)->id()];
  }, $keys);
}

function nodeRefs(array $keys, array $nodes): array {
  return array_map(static function (string $key) use ($nodes): array {
    return ['target_id' => $nodes[$key]->id()];
  }, $keys);
}

function slug(string $value): string {
  $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $value), '-'));
  return $slug ?: 'sample';
}
