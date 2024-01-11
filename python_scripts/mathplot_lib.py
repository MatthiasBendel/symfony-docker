import matplotlib.pyplot as plt

# Sample data
x = [1, 2, 3, 4, 5]
y = [2, 3, 5, 7, 11]

# Plot the data points
plt.plot(x, y, 'b-', label='Data')

# Mark specific data points with a red circle
special_positions = [2, 4,3]
plt.scatter([x[i-1] for i in special_positions], [y[i-1] for i in special_positions], color='r', label='Special Positions')

# Add labels and title
plt.xlabel('X')
plt.ylabel('Y')
plt.title('Data Points with Special Positions Marked')
plt.legend()

# Display the plot
plt.show()


if __name__ == '__main__':
    log_everything()
