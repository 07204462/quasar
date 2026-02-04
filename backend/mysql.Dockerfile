FROM mysql:8.0

# Set environment variables
ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_DATABASE=app_db
ENV MYSQL_USER=app_user
ENV MYSQL_PASSWORD=secret

# Copy any custom MySQL configuration files if needed
# COPY my.cnf /etc/mysql/conf.d/

# Expose MySQL port
EXPOSE 3306

# Health check to ensure MySQL is ready
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
  CMD mysqladmin ping -h localhost -u root -p${MYSQL_ROOT_PASSWORD} || exit 1

# Use the default MySQL entrypoint and command