# SMS Campaigns, Voice Broadcasts & Outbound Routing with AI

## Overview

This guide covers the advanced communication features integrated into the FusionPBX Laravel UI, including SMS campaigns, voice broadcasts, and intelligent outbound routing powered by AI.

## Table of Contents

1. [SMS Campaigns](#sms-campaigns)
2. [Voice Broadcasts](#voice-broadcasts)
3. [Outbound Routing](#outbound-routing)
4. [AI Integration](#ai-integration)
5. [Configuration](#configuration)
6. [API Usage](#api-usage)

---

## SMS Campaigns

### Features

- **Bulk SMS Messaging**: Send personalized SMS to thousands of contacts
- **AI-Powered Auto-Reply**: Automatically respond to customer messages using AI
- **Sentiment Analysis**: Understand customer emotions in real-time
- **Intent Detection**: Identify what customers want
- **Template Variables**: Personalize messages with `{first_name}`, `{last_name}`, etc.
- **Scheduling**: Send immediately, schedule for later, or set up recurring campaigns
- **Delivery Tracking**: Monitor sent, delivered, and failed messages

### Creating an SMS Campaign

1. Navigate to **Campaigns & AI** > **SMS Campaigns**
2. Click **Create Campaign**
3. Fill in the required fields:
   - **Domain**: Select your domain
   - **Campaign Name**: Give it a descriptive name
   - **SMS Template**: Write your message (use variables for personalization)
   - **Sender ID**: Your sender identification (max 11 characters)
   - **Sending Rate**: Messages per minute (1-100)

4. **Enable AI Features** (Optional):
   - ☑️ **Enable AI-Powered Features**
   - Select **AI Model**: GPT-3.5 Turbo (fast) or GPT-4 (advanced)
   - ☑️ **Auto-Reply to Responses**: AI will automatically respond to incoming messages
   - **AI Personality**: Describe how AI should respond (e.g., "friendly and helpful")

5. Select **Schedule Type**:
   - **Immediate**: Send right away
   - **Scheduled**: Pick a date/time
   - **Recurring**: Set up periodic campaigns

6. Click **Create Campaign**

### AI-Powered SMS Features

#### Auto-Reply Handling
When enabled, the AI agent will:
- Read incoming SMS messages
- Understand the intent and sentiment
- Generate appropriate responses based on personality settings
- Track conversation context across multiple messages

#### Sentiment Analysis
Every message is analyzed for:
- **Positive**: Happy, satisfied customers
- **Neutral**: Informational queries
- **Negative**: Complaints, issues

#### Intent Detection
AI identifies customer intentions:
- Purchase inquiries
- Support requests
- Cancellation requests
- General information

### SMS Campaign Workflow

```
1. Create Campaign → 2. Add Contacts → 3. Start Campaign
                                           ↓
                           4. Messages Sent → 5. Delivery Tracking
                                           ↓
                           6. Incoming Replies → 7. AI Auto-Response
```

---

## Voice Broadcasts

### Features

- **Automated Calling**: Call thousands of contacts simultaneously
- **Text-to-Speech (TTS)**: Convert text to natural-sounding voice
- **Audio Upload**: Use pre-recorded messages
- **Interactive AI Conversations**: Two-way conversations with AI
- **Speech Recognition**: Understand what recipients say
- **Multi-Turn Conversations**: Up to 20 conversation turns
- **Call Analytics**: Track answered, failed, and completed calls

### Creating a Voice Broadcast

1. Navigate to **Campaigns & AI** > **Voice Broadcasts**
2. Click **Create Broadcast**
3. Fill in basic details:
   - **Domain**: Select your domain
   - **Broadcast Name**: Give it a name
   - **Max Retries**: How many times to retry failed calls (1-10)

4. **Choose Audio Content**:
   
   **Option A: Text-to-Speech (AI Generated)**
   - ☑️ **Use Text-to-Speech**
   - **Message Text**: Enter the script
   - **Voice**: Select from 6 AI voices:
     - **Alloy**: Neutral, professional
     - **Echo**: Male, clear
     - **Fable**: British accent
     - **Onyx**: Deep, authoritative
     - **Nova**: Female, warm
     - **Shimmer**: Friendly, upbeat

   **Option B: Audio File Upload**
   - Upload MP3 or WAV file (max 10MB)

5. **Enable AI Conversation Features** (Optional):
   - ☑️ **Enable AI Agent**
   - ☑️ **Interactive Conversation Mode**: AI will listen and respond
   - **AI System Prompt**: Instructions for AI behavior
   - **Max Conversation Turns**: 1-20 (default: 5)

6. **Caller ID Settings**:
   - Set caller ID name and number

7. Click **Create Broadcast**

### AI Conversation Mode

When enabled, the system:
1. **Plays Initial Message**: TTS or uploaded audio
2. **Listens to Response**: Speech-to-text transcription
3. **AI Processes**: Understands intent and sentiment
4. **Generates Response**: Natural, contextual reply
5. **Converts to Speech**: TTS of AI response
6. **Plays to Caller**: Seamless conversation
7. **Repeats**: Up to max conversation turns

### Voice Broadcast Analytics

Track:
- **Total Contacts**: Numbers in campaign
- **Called Count**: Calls attempted
- **Answered Count**: Calls answered
- **Completion Rate**: Percentage of successful calls
- **AI Conversations**: How many interactive conversations occurred
- **Avg Conversation Length**: Average turns per conversation
- **Sentiment Analysis**: Overall conversation sentiment

---

## Outbound Routing

### Features

- **Pattern Matching**: Route calls based on number patterns
- **Priority Ordering**: Lower order = higher priority
- **AI Route Optimization**: Intelligent route selection
- **Cost Optimization**: Choose cheapest routes automatically
- **Quality Optimization**: Prefer high-quality carriers
- **Multi-Modal**: Support voice, SMS, or both
- **Emergency Routing**: Special handling for emergency numbers

### Creating an Outbound Route

1. Navigate to **DIDs & Routes** > **Outbound Routes**
2. Click **Create Route**
3. Configure basic settings:
   - **Domain**: Select your domain
   - **Route Name**: Descriptive name
   - **Route Order**: 0 = highest priority
   - **Destination Pattern**: Regex pattern (e.g., `^(\d{11})$`)
   - **Route Type**: Voice Only, SMS Only, or Both
   - **Gateway Name**: Target gateway

4. **AI-Powered Routing** (Optional):
   - ☑️ **Enable AI Route Optimization**
   - ☑️ **Cost Optimization**: Prefer lower-cost routes
   - ☑️ **Quality Optimization**: Prefer higher-quality routes

5. **Additional Options**:
   - **Dial Prefix**: Prefix to add (e.g., "1" for US)
   - **Prefix Strip**: Digits to strip from number
   - **Caller ID**: Override caller identification

6. Click **Create Route**

### Route Pattern Examples

| Pattern | Description | Matches |
|---------|-------------|---------|
| `^(\d{11})$` | 11-digit numbers | 12125551234 |
| `^1(\d{10})$` | US numbers with 1 | 12125551234 |
| `^(\d{10})$` | 10-digit numbers | 2125551234 |
| `^011(\d+)$` | International (011) | 011441234567 |
| `^[2-9]\d{9}$` | US local numbers | 2125551234 |

### AI Route Optimization

The AI analyzes:
- **Historical Performance**: Past success rates
- **Cost Data**: Per-minute pricing
- **Quality Metrics**: Call quality scores
- **Time of Day**: Peak/off-peak patterns
- **Destination**: Geographic considerations
- **Carrier Load**: Current capacity

Decision Algorithm:
```
If AI_ENABLED:
    routes = get_matching_routes(destination)
    
    if COST_OPTIMIZATION:
        weight_cost = 0.7
    else:
        weight_cost = 0.3
    
    if QUALITY_OPTIMIZATION:
        weight_quality = 0.7
    else:
        weight_quality = 0.3
    
    score = (cost_score * weight_cost) + (quality_score * weight_quality)
    return highest_scoring_route
```

### Route Testing

Test route matching:
```bash
POST /outbound-routes/test
{
    "destination_number": "12125551234"
}

Response:
{
    "matched_route": {
        "route_name": "US Long Distance",
        "gateway_name": "gateway-1",
        "ai_enabled": true
    },
    "ai_recommendation": "This route offers best cost-quality balance..."
}
```

---

## AI Integration

### Supported AI Models

1. **GPT-4**: Most advanced, best for complex conversations
2. **GPT-3.5 Turbo**: Fast, cost-effective, good for most use cases
3. **Whisper**: Speech-to-text transcription

### AI Service Features

#### 1. SMS Response Generation
```php
$aiService->generateSMSResponse($inboundMessage, [
    'system_prompt' => 'You are a helpful customer service agent.',
    'personality' => 'friendly and professional'
]);
```

#### 2. Sentiment Analysis
```php
$result = $aiService->analyzeSentiment($text);
// Returns: positive, negative, or neutral
```

#### 3. Text-to-Speech
```php
$audio = $aiService->textToSpeech($text, $voice);
// Generates MP3 audio file
```

#### 4. Speech-to-Text
```php
$transcription = $aiService->speechToText($audioFilePath);
// Returns text transcription
```

#### 5. Route Optimization
```php
$recommendation = $aiService->optimizeRoute(
    $destinationNumber,
    $availableRoutes,
    'cost' // or 'quality'
);
```

### AI Configuration

Add to `.env`:
```env
OPENAI_API_KEY=sk-your-api-key-here
OPENAI_MODEL=gpt-4
OPENAI_BASE_URL=https://api.openai.com/v1
```

### Cost Management

Estimated API costs (as of 2024):
- **GPT-3.5 Turbo**: $0.0010 per 1K tokens (~$0.001 per SMS response)
- **GPT-4**: $0.03 per 1K tokens (~$0.03 per complex conversation)
- **Whisper**: $0.006 per minute of audio
- **TTS**: $0.015 per 1K characters

**Budget Planning**:
- 1,000 SMS with AI replies: ~$1-2
- 1,000 voice broadcasts with AI conversations (5 min avg): ~$30-50
- Route optimization (per call): <$0.01

---

## Configuration

### Database Setup

Run migrations:
```bash
cd laravel-ui
php artisan migrate
```

This creates:
- `sms_campaigns` - SMS campaign management
- `sms_messages` - Individual message tracking
- `voice_broadcasts` - Voice broadcast campaigns
- `broadcast_messages` - Call tracking and recordings
- `outbound_routes` - Routing configuration
- `sms_campaign_contacts` - Campaign-contact relationships
- `broadcast_contacts` - Broadcast-contact relationships

### Environment Variables

```env
# OpenAI Configuration
OPENAI_API_KEY=sk-your-key-here
OPENAI_MODEL=gpt-4
OPENAI_BASE_URL=https://api.openai.com/v1

# SMS Provider (Optional)
SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=your-sid
TWILIO_AUTH_TOKEN=your-token
TWILIO_FROM_NUMBER=+1234567890

# Voice Provider (Optional)
VOICE_PROVIDER=twilio
```

### Storage Configuration

Configure storage for audio files:
```bash
# Create storage link
php artisan storage:link

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

---

## API Usage

### SMS Campaign API

**Create Campaign**:
```bash
POST /sms-campaigns
Content-Type: application/json

{
    "domain_uuid": "uuid-here",
    "campaign_name": "Summer Sale 2024",
    "sms_template": "Hi {first_name}! Summer sale now on - 50% off!",
    "sender_id": "MySale",
    "sending_rate": 10,
    "schedule_type": "immediate",
    "ai_enabled": true,
    "ai_model": "gpt-3.5-turbo",
    "ai_reply_handling": true,
    "ai_personality": "Friendly sales assistant"
}
```

**Start Campaign**:
```bash
POST /sms-campaigns/{id}/start
```

**Pause Campaign**:
```bash
POST /sms-campaigns/{id}/pause
```

### Voice Broadcast API

**Create Broadcast**:
```bash
POST /voice-broadcasts
Content-Type: multipart/form-data

{
    "domain_uuid": "uuid-here",
    "broadcast_name": "Payment Reminder",
    "text_to_speech": true,
    "tts_text": "This is a friendly reminder about your payment.",
    "tts_voice": "nova",
    "ai_enabled": true,
    "ai_conversation_mode": true,
    "ai_system_prompt": "You are a payment collection agent. Be polite but firm.",
    "ai_max_conversation_turns": 5
}
```

### Outbound Route API

**Create Route**:
```bash
POST /outbound-routes
Content-Type: application/json

{
    "domain_uuid": "uuid-here",
    "route_name": "US Calls",
    "route_order": 1,
    "destination_pattern": "^1(\\d{10})$",
    "gateway_name": "gateway-us",
    "route_type": "voice",
    "ai_routing_enabled": true,
    "ai_cost_optimization": true
}
```

**Test Route**:
```bash
POST /outbound-routes/test
Content-Type: application/json

{
    "destination_number": "12125551234"
}
```

---

## Best Practices

### SMS Campaigns

1. **Personalization**: Always use template variables
2. **Timing**: Send during business hours (9 AM - 8 PM)
3. **Opt-Out**: Include unsubscribe option
4. **Rate Limiting**: Don't exceed carrier limits
5. **AI Personality**: Be consistent with brand voice

### Voice Broadcasts

1. **Script Length**: Keep under 30 seconds for initial message
2. **Voice Selection**: Match voice to audience
3. **AI Conversations**: Limit to 5 turns for efficiency
4. **Call Windows**: Respect time zones and business hours
5. **Recording**: Always announce if call is recorded

### Outbound Routing

1. **Route Order**: Most specific patterns first
2. **Testing**: Test patterns before going live
3. **Backup Routes**: Always have fallback options
4. **AI Optimization**: Enable for high-volume routes
5. **Monitoring**: Track success rates and adjust

---

## Troubleshooting

### SMS Issues

**Messages Not Sending**:
- Check sender ID is approved
- Verify rate limits not exceeded
- Ensure adequate balance

**AI Not Responding**:
- Verify OpenAI API key is valid
- Check API quota not exceeded
- Review AI system prompts

### Voice Broadcast Issues

**TTS Failing**:
- Check text length (max 4000 characters)
- Verify voice name is correct
- Ensure API key has TTS access

**AI Conversation Not Working**:
- Enable both AI agent and conversation mode
- Check system prompt is clear
- Verify max turns > 0

### Routing Issues

**No Route Matching**:
- Test patterns with route testing tool
- Check route is active
- Verify route order

**AI Optimization Not Working**:
- Enable AI routing in route settings
- Check optimization preferences
- Verify sufficient historical data

---

## Support & Resources

- **Documentation**: `/laravel-ui/README.md`
- **API Reference**: `/laravel-ui/routes/web.php`
- **Configuration**: `.env.example`
- **Migration Files**: `/laravel-ui/database/migrations/`

For additional help, refer to the main README or consult the FusionPBX documentation.

---

**Version**: 1.0.0  
**Last Updated**: 2024  
**Powered by**: Laravel 11, OpenAI GPT-4, Whisper, TTS
