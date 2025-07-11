# site-notification-bar

Display a notice bar on your WP home page.

<img width="640" alt="site-notification-bar" src="https://github.com/user-attachments/assets/4f4bff9e-c2ea-4afe-8012-1dc736ab17ef" />

## Site Notification Bar?

This plugin makes it super easy to display a notifiction bar on the front-end. It also allows you to customize how the notification bar looks like.

<img width="720" alt="Screenshot 2025-07-09 at 02 55 58" src="https://github.com/user-attachments/assets/c99c9d61-38b6-40b8-9e1b-f18065898888" />

### Hooks

#### `site_notification_bar_admin_fields`

This custom hook (filter) provides a way to filter the admin fields presented on the options page of the plugin.

```php
add_filter( 'site_notification_bar_admin_fields', [ $this, 'custom_admin_fields' ] );

public function custom_admin_fields( $fields ): array {
    $fields[] = [
        'name'    => 'name_of_your_control',
        'label'   => __( 'Control Label', 'your-text-domain' ),
        'cb'      => [ $this, 'name_of_your_control_callback' ],
        'page'    => 'site-notification-bar',
        'section' => 'site_notice_section',
    ];

    return $fields;
}
```

**Parameters**

- options _`{array}`_ By default this will be an array containing key, value options for the control.
<br/>

#### `site_notification_bar_settings`

This custom hook (filter) provides a way to customise the settings used by the notification bar.

```php
add_filter( 'site_notification_bar_settings', [ $this, 'custom_bar_settings' ] );

public function bar_settings( $settings ): array {
    $settings['site_notice_text'] = esc_html(
        'Lorem ipsum doloar sit amet <a href="example.com"/>aquila siento</a>'
    );

    return $settings;
}
```

**Parameters**

- options _`{array}`_ By default this will be an associative array containing key, value options of the settings used by the notification bar on the front-end.
<br/>

## Contribute

Contributions are __welcome__ and will be fully __credited__. To contribute, please fork this repo and raise a PR (Pull Request) against the `master` branch.

### Pre-requisites

You should have the following tools before proceeding to the next steps:

- Composer
- Yarn
- Docker

To enable you start development, please run:

```bash
yarn start
```

This should spin up a local WP env instance for you to work with at:

```bash
http://site-notification-bar.localhost:8484
```

You should now have a functioning local WP env to work with. To login to the `wp-admin` backend, please use `admin` for username & `password` for password.

__Awesome!__ - Thanks for being interested in contributing your time and code to this project!
