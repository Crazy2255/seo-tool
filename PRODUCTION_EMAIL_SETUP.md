# Production Email Setup Guide

## SendGrid Setup (Recommended)

1. **Sign up for SendGrid**: https://sendgrid.com (Free tier: 100 emails/day)

2. **Create API Key**:
   - Go to Settings > API Keys
   - Create new API Key with "Full Access"
   - Copy the API key

3. **Update .env**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"
```

4. **Verify Sender Identity**:
   - Go to Settings > Sender Authentication
   - Verify your email address or domain

## Domain Authentication (Optional but Recommended)

Add these DNS records to your domain:

**SPF Record:**
```
v=spf1 include:sendgrid.net ~all
```

**DKIM Records:**
(SendGrid will provide these after domain verification)

## Email Content Best Practices

1. **Avoid spam triggers**:
   - Don't use ALL CAPS
   - Avoid excessive exclamation marks
   - Include unsubscribe link

2. **Professional templates**:
   - Use clean HTML
   - Include your company info
   - Add social media links

3. **Track engagement**:
   - Monitor open rates
   - Track click-through rates
   - Handle bounces properly

## Testing Checklist

- [ ] Test email reaches inbox (not spam)
- [ ] Test with different email providers (Gmail, Outlook, Yahoo)
- [ ] Verify unsubscribe link works
- [ ] Check email formatting on mobile
- [ ] Test with real user email addresses
