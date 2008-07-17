{ts 1=$contact.display_name}Dear %1{/ts},

{ts 1=$next_payment|crmDate}This is a reminder that the next payment on your pledge is due on %1.{/ts}

===========================================================
{ts}Current Payment{/ts}

===========================================================
{ts}Amount Due{/ts} : {$amount_due|crmMoney}
{ts}Due Date{/ts} : {$scheduled_payment_date|crmDate}

{if $payment_instrument NEQ 'Credit Card'}
{ts}Please mail your payment to{/ts}:
{$domain.address}
{else}
{ts 2=$domain.phone}Please contact us at %2 to make your payment.{/ts}
{/if}
{if $contribution_id}
{capture assign=contributionUrl}
  {crmURL p='civicrm/contribute/transact' q="reset=1&id=`$contribution_id`&cs=`$checksumValue`&pledgeId=`$pledge_id`"}
{/capture}
Please follow the link to pay the outstanding amount <a href="{$contributionUrl}">{ts}Pay Online{/ts}</a>
{/if}
===========================================================
{ts}Pledge Information{/ts}

===========================================================
{ts}Pledge Received{/ts} : {$create_date|crmDate}
{ts}Total Pledge Amount{/ts} : {$amount|crmMoney}
{ts}Total Paid{/ts} : {$amount_paid|crmMoney}

{ts 1=$domain.phone 2=$domain.email}Please contact us at %1 or send email to  <a href="mailto:%2">%2</a> if you have questions
or need to modify your payment schedule.{/ts}

{ts}Thank your for your generous support.{/ts}